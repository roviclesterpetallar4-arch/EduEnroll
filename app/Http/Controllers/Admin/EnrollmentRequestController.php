<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EnrollmentApprovedMail;
use App\Models\AcademicPeriod;
use App\Models\Department;
use App\Models\EnrollmentRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class EnrollmentRequestController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('is_admin', false)
            ->where(function ($query): void {
                $query->whereHas('enrollmentRequest')
                    ->orWhereHas('courseEnrollmentRequests');
            })
            ->with([
                'enrollmentRequest',
                'enrollmentRequest.academicPeriod',
                'enrollmentRequest.department',
                'studentProfile',
                'courseEnrollmentRequests.course',
            ])
            ->latest()
            ->paginate(5);

        $statusCounts = EnrollmentRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $metrics = [
            'total' => $statusCounts->sum(),
            'pending' => $statusCounts->get(EnrollmentRequest::STATUS_PENDING, 0),
            'approved' => $statusCounts->get(EnrollmentRequest::STATUS_APPROVED, 0),
            'rejected' => $statusCounts->get(EnrollmentRequest::STATUS_REJECTED, 0),
        ];
        $academicPeriods = AcademicPeriod::query()
            ->orderByDesc('year_start')
            ->get();
        $organizations = Department::query()
            ->whereIn('code', ['EDSO', 'ITO', 'TMS', 'HMS', 'JMS'])
            ->orderBy('code')
            ->get();
        $selectedHistoryPeriod = $academicPeriods->firstWhere(
            'id',
            request()->query('history_period', $academicPeriods->firstWhere('is_active', true)?->id)
        );
        $acceptedStudentsByOrganization = $organizations->mapWithKeys(function (Department $organization) use ($selectedHistoryPeriod) {
            $acceptedStudents = EnrollmentRequest::query()
                ->with(['user', 'academicPeriod'])
                ->where('status', EnrollmentRequest::STATUS_APPROVED)
                ->where('department_id', $organization->id)
                ->whereNotNull('academic_period_id')
                ->when(
                    $selectedHistoryPeriod,
                    fn ($query) => $query->where('academic_period_id', $selectedHistoryPeriod->id),
                    fn ($query) => $query->whereRaw('1 = 0')
                )
                ->orderByDesc('accepted_at')
                ->get();

            return [$organization->id => $acceptedStudents];
        });
        $legacyAcceptedRequests = EnrollmentRequest::query()
            ->with(['user', 'department', 'academicPeriod'])
            ->where('status', EnrollmentRequest::STATUS_APPROVED)
            ->where(function ($query) use ($organizations): void {
                $query->whereNull('academic_period_id')
                    ->orWhereNull('department_id')
                    ->orWhereNotIn('department_id', $organizations->modelKeys());
            })
            ->orderByDesc('accepted_at')
            ->get();
        $legacyAcceptedCount = $legacyAcceptedRequests->count();

        return view('admin.enrollment-requests', compact(
            'students',
            'metrics',
            'academicPeriods',
            'organizations',
            'selectedHistoryPeriod',
            'acceptedStudentsByOrganization',
            'legacyAcceptedRequests',
            'legacyAcceptedCount'
        ));
    }

    public function recordHistory(Request $request, EnrollmentRequest $enrollmentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'academic_period_id' => ['required', 'integer', 'exists:academic_periods,id'],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->whereIn('code', ['EDSO', 'ITO', 'TMS', 'HMS', 'JMS']),
            ],
        ]);

        try {
            $recorded = DB::transaction(function () use ($enrollmentRequest, $validated): bool {
                $enrollment = EnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($enrollmentRequest->id);
                $hasValidDepartment = $enrollment->department_id
                    && Department::query()
                        ->whereIn('code', ['EDSO', 'ITO', 'TMS', 'HMS', 'JMS'])
                        ->whereKey($enrollment->department_id)
                        ->exists();

                if (
                    $enrollment->status !== EnrollmentRequest::STATUS_APPROVED
                    || ($enrollment->academic_period_id && $hasValidDepartment)
                ) {
                    return false;
                }

                $enrollment->update([
                    'academic_period_id' => $enrollment->academic_period_id ?? $validated['academic_period_id'],
                    'department_id' => $hasValidDepartment ? $enrollment->department_id : $validated['department_id'],
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to record accepted enrollment history.', [
                'enrollment_request_id' => $enrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The accepted student history could not be recorded. Please try again.');
        }

        if (! $recorded) {
            return back()->with('error', 'Only accepted students with missing history details can be updated.');
        }

        return back()->with('success', 'The accepted student was added to enrollment history.');
    }

    public function accept(Request $request, EnrollmentRequest $enrollmentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'academic_period_id' => ['required', 'integer', 'exists:academic_periods,id'],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->whereIn('code', ['EDSO', 'ITO', 'TMS', 'HMS', 'JMS']),
            ],
        ]);

        try {
            $approved = DB::transaction(function () use ($enrollmentRequest, $validated): bool {
                $request = EnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($enrollmentRequest->id);

                if ($request->status !== EnrollmentRequest::STATUS_PENDING) {
                    return false;
                }

                $request->update([
                    'status' => EnrollmentRequest::STATUS_APPROVED,
                    'academic_period_id' => $validated['academic_period_id'],
                    'department_id' => $validated['department_id'],
                    'accepted_at' => now(),
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to approve enrollment request.', [
                'enrollment_request_id' => $enrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The enrollment request could not be approved. Please try again.');
        }

        if (! $approved) {
            return back()->with('error', 'Only pending enrollment requests can be approved.');
        }

        try {
            $approvedRequest = $enrollmentRequest->fresh(['user', 'academicPeriod', 'department']);
            Mail::to($approvedRequest->user->email)->send(new EnrollmentApprovedMail($approvedRequest));
        } catch (Throwable $exception) {
            Log::error('Enrollment was approved but its email notification could not be delivered.', [
                'enrollment_request_id' => $enrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('warning', 'The student was approved, but the confirmation email could not be delivered. Check the mail settings and application log.');
        }

        return back()->with('success', 'The student was approved and a confirmation email was queued.');
    }

    public function reject(EnrollmentRequest $enrollmentRequest): RedirectResponse
    {
        try {
            $rejected = DB::transaction(function () use ($enrollmentRequest): bool {
                $request = EnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($enrollmentRequest->id);

                if ($request->status !== EnrollmentRequest::STATUS_PENDING) {
                    return false;
                }

                $request->update(['status' => EnrollmentRequest::STATUS_REJECTED]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to reject enrollment request.', [
                'enrollment_request_id' => $enrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The enrollment request could not be rejected. Please try again.');
        }

        if (! $rejected) {
            return back()->with('error', 'Only pending enrollment requests can be rejected.');
        }

        return back()->with('success', 'The enrollment request was rejected.');
    }
}
