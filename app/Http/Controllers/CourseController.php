<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollmentRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * List all courses with optional search and department filtering.
     */
    public function index(Request $request): View
    {
        $query = Course::with('department');

        // Search by course title, code, or instructor
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('instructor', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Department ID
        if ($departmentId = $request->input('department')) {
            $query->where('department_id', $departmentId);
        }

        $courses = $query->orderBy('code', 'asc')->get();
        $departments = Department::orderBy('id', 'asc')->get();
        $selectedCourseId = null;

        if ($request->user() && ! $request->user()->isAdmin()) {
            $selectedCourseId = $request->user()->courseEnrollmentRequests()
                ->whereIn('status', [
                    CourseEnrollmentRequest::STATUS_PENDING,
                    CourseEnrollmentRequest::STATUS_APPROVED,
                    CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
                ])
                ->value('course_id')
                ?? $request->user()->courses()->value('courses.id');
        }

        return view('courses.index', compact('courses', 'departments', 'selectedCourseId'));
    }

    /**
     * Display course details and the signed-in student's enrollment status.
     */
    public function show(int|string $id): View
    {
        $course = Course::with('department')->findOrFail($id);
        $isEnrolled = auth()->check()
            && ! auth()->user()->isAdmin()
            && $course->students()->where('users.id', auth()->id())->exists();

        $courseEnrollmentRequest = auth()->check() && ! auth()->user()->isAdmin()
            ? auth()->user()->courseEnrollmentRequests()->where('course_id', $course->id)->first()
            : null;
        $studentCourse = auth()->check() && ! auth()->user()->isAdmin()
            ? auth()->user()->courses()->with('department')->first()
            : null;
        $activeCourseRequest = auth()->check() && ! auth()->user()->isAdmin()
            ? auth()->user()->courseEnrollmentRequests()
                ->with('course')
                ->whereIn('status', [
                    CourseEnrollmentRequest::STATUS_PENDING,
                    CourseEnrollmentRequest::STATUS_APPROVED,
                    CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
                ])
                ->first()
            : null;

        return view('courses.show', compact('course', 'isEnrolled', 'courseEnrollmentRequest', 'studentCourse', 'activeCourseRequest'));
    }

    /**
     * Submit a course enrollment request for the authenticated student.
     */
    public function enroll(Request $request, int|string $courseId): RedirectResponse
    {
        $student = $request->user();

        if ($student->isAdmin()) {
            abort(403, 'Administrators cannot submit student enrollment requests.');
        }

        $result = DB::transaction(function () use ($student, $courseId): string {
            User::query()->lockForUpdate()->findOrFail($student->id);
            $course = Course::query()->lockForUpdate()->findOrFail($courseId);

            if ($course->students()->where('users.id', $student->id)->exists()) {
                return 'already_enrolled';
            }

            $otherEnrollment = $student->courses()
                ->where('courses.id', '!=', $course->id)
                ->exists();
            $otherActiveRequest = $student->courseEnrollmentRequests()
                ->where('course_id', '!=', $course->id)
                ->whereIn('status', [
                    CourseEnrollmentRequest::STATUS_PENDING,
                    CourseEnrollmentRequest::STATUS_APPROVED,
                    CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
                ])
                ->exists();

            if ($otherEnrollment || $otherActiveRequest) {
                return 'another_course_active';
            }

            $enrollmentRequest = CourseEnrollmentRequest::query()
                ->where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->lockForUpdate()
                ->first();

            if ($enrollmentRequest?->status === CourseEnrollmentRequest::STATUS_PENDING) {
                return 'already_pending';
            }

            if ($enrollmentRequest) {
                $enrollmentRequest->update(['status' => CourseEnrollmentRequest::STATUS_PENDING]);
            } else {
                CourseEnrollmentRequest::create([
                    'course_id' => $course->id,
                    'user_id' => $student->id,
                    'status' => CourseEnrollmentRequest::STATUS_PENDING,
                ]);
            }

            return 'submitted';
        });

        if ($result === 'already_enrolled') {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        if ($result === 'already_pending') {
            return back()->with('error', 'Your enrollment request is already awaiting review.');
        }

        if ($result === 'another_course_active') {
            return back()->with('error', 'You can only request one course at a time. Cancel your current course or request first.');
        }

        return redirect()->route('courses.show', $courseId)
            ->with('success', 'Your enrollment request was sent to the NCC administration for review.');
    }

    public function cancelEnrollmentRequest(Request $request, int|string $courseId): RedirectResponse
    {
        $student = $request->user();

        if ($student->isAdmin()) {
            abort(403, 'Administrators cannot cancel student course enrollments.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'in:'.implode(',', array_keys(CourseEnrollmentRequest::CANCELLATION_REASONS))],
            'cancellation_details' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($student, $courseId, $validated): string {
            User::query()->lockForUpdate()->findOrFail($student->id);
            $course = Course::query()->lockForUpdate()->findOrFail($courseId);
            $isEnrolled = $course->students()->where('users.id', $student->id)->exists();
            $enrollmentRequest = CourseEnrollmentRequest::query()
                ->where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->lockForUpdate()
                ->first();

            if (! $isEnrolled && $enrollmentRequest?->status === CourseEnrollmentRequest::STATUS_PENDING) {
                $enrollmentRequest->update([
                    'status' => CourseEnrollmentRequest::STATUS_CANCELLED,
                    'cancellation_reason' => $validated['cancellation_reason'],
                    'cancellation_details' => $validated['cancellation_details'] ?? null,
                ]);

                return 'request_cancelled';
            }

            if ($enrollmentRequest?->status === CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING) {
                return 'cancellation_already_pending';
            }

            if ($isEnrolled) {
                if (! $enrollmentRequest) {
                    $enrollmentRequest = CourseEnrollmentRequest::create([
                        'course_id' => $course->id,
                        'user_id' => $student->id,
                        'status' => CourseEnrollmentRequest::STATUS_APPROVED,
                    ]);
                }

                $enrollmentRequest->update([
                    'status' => CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
                    'cancellation_reason' => $validated['cancellation_reason'],
                    'cancellation_details' => $validated['cancellation_details'] ?? null,
                ]);

                return 'cancellation_requested';
            }

            return 'not_cancellable';
        });

        return match ($result) {
            'request_cancelled' => back()->with('success', 'Your pending course request was cancelled. You may now request another course.'),
            'cancellation_already_pending' => back()->with('error', 'Your cancellation request is already awaiting admin review.'),
            'cancellation_requested' => back()->with('success', 'Your cancellation request was sent to NCC administration. You may request another course after it is approved.'),
            default => back()->with('error', 'There is no active enrollment request to cancel.'),
        };
    }

    public function signInForEnrollment(int|string $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);

        if (auth()->check()) {
            return redirect()->route('courses.show', $course);
        }

        session()->put('url.intended', route('courses.show', $course));

        return redirect()->route('login');
    }
}
