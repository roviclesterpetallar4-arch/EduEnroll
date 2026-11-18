<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CourseEnrollmentStatusMail;
use App\Models\CourseEnrollmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CourseEnrollmentRequestController extends Controller
{
    public function approve(CourseEnrollmentRequest $courseEnrollmentRequest): RedirectResponse
    {
        try {
            $approved = DB::transaction(function () use ($courseEnrollmentRequest): bool {
                $enrollmentRequest = CourseEnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($courseEnrollmentRequest->id);

                if ($enrollmentRequest->status !== CourseEnrollmentRequest::STATUS_PENDING) {
                    return false;
                }

                $user = $enrollmentRequest->user()->lockForUpdate()->firstOrFail();
                $course = $enrollmentRequest->course()->lockForUpdate()->firstOrFail();
                $hasAnotherEnrollment = $user->courses()
                    ->where('courses.id', '!=', $course->id)
                    ->exists();
                $hasAnotherActiveRequest = $user->courseEnrollmentRequests()
                    ->where('course_id', '!=', $course->id)
                    ->whereIn('status', [
                        CourseEnrollmentRequest::STATUS_PENDING,
                        CourseEnrollmentRequest::STATUS_APPROVED,
                        CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
                    ])
                    ->exists();

                if ($hasAnotherEnrollment || $hasAnotherActiveRequest) {
                    return false;
                }

                if (! $course->students()->where('users.id', $enrollmentRequest->user_id)->exists()) {
                    $course->students()->attach($enrollmentRequest->user_id, [
                        'status' => 'enrolled',
                    ]);
                }

                $enrollmentRequest->update([
                    'status' => CourseEnrollmentRequest::STATUS_APPROVED,
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to approve course enrollment request.', [
                'course_enrollment_request_id' => $courseEnrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The course enrollment request could not be approved. Please try again.');
        }

        if (! $approved) {
            return back()->with('error', 'This request is no longer pending or the student already has another active course request or enrollment.');
        }

        $emailSent = $this->sendStatusEmail($courseEnrollmentRequest, CourseEnrollmentStatusMail::ENROLLMENT_APPROVED);

        return back()->with(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'The student was enrolled in the course and an email notification was sent.'
                : 'The student was enrolled, but the email notification could not be delivered. Check the mail settings and application log.',
        );
    }

    public function reject(CourseEnrollmentRequest $courseEnrollmentRequest): RedirectResponse
    {
        try {
            $rejected = DB::transaction(function () use ($courseEnrollmentRequest): bool {
                $enrollmentRequest = CourseEnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($courseEnrollmentRequest->id);

                if ($enrollmentRequest->status !== CourseEnrollmentRequest::STATUS_PENDING) {
                    return false;
                }

                $enrollmentRequest->update([
                    'status' => CourseEnrollmentRequest::STATUS_REJECTED,
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to reject course enrollment request.', [
                'course_enrollment_request_id' => $courseEnrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The course enrollment request could not be rejected. Please try again.');
        }

        if (! $rejected) {
            return back()->with('error', 'Only pending course enrollment requests can be rejected.');
        }

        $emailSent = $this->sendStatusEmail($courseEnrollmentRequest, CourseEnrollmentStatusMail::ENROLLMENT_REJECTED);

        return back()->with(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'The course enrollment request was rejected and an email notification was sent.'
                : 'The request was rejected, but the email notification could not be delivered. Check the mail settings and application log.',
        );
    }

    public function approveCancellation(CourseEnrollmentRequest $courseEnrollmentRequest): RedirectResponse
    {
        try {
            $cancelled = DB::transaction(function () use ($courseEnrollmentRequest): bool {
                $enrollmentRequest = CourseEnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($courseEnrollmentRequest->id);

                if ($enrollmentRequest->status !== CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING) {
                    return false;
                }

                $enrollmentRequest->user()->lockForUpdate()->firstOrFail();
                $course = $enrollmentRequest->course()->lockForUpdate()->firstOrFail();
                $course->students()->detach($enrollmentRequest->user_id);
                $enrollmentRequest->update([
                    'status' => CourseEnrollmentRequest::STATUS_CANCELLED,
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to approve course enrollment cancellation.', [
                'course_enrollment_request_id' => $courseEnrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The course cancellation could not be approved. Please try again.');
        }

        if (! $cancelled) {
            return back()->with('error', 'Only pending cancellation requests can be approved.');
        }

        $emailSent = $this->sendStatusEmail($courseEnrollmentRequest, CourseEnrollmentStatusMail::CANCELLATION_APPROVED);

        return back()->with(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'The course enrollment was cancelled, the student may request another course, and an email notification was sent.'
                : 'The course enrollment was cancelled, but the email notification could not be delivered. Check the mail settings and application log.',
        );
    }

    public function rejectCancellation(CourseEnrollmentRequest $courseEnrollmentRequest): RedirectResponse
    {
        try {
            $rejected = DB::transaction(function () use ($courseEnrollmentRequest): bool {
                $enrollmentRequest = CourseEnrollmentRequest::query()
                    ->lockForUpdate()
                    ->findOrFail($courseEnrollmentRequest->id);

                if ($enrollmentRequest->status !== CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING) {
                    return false;
                }

                $enrollmentRequest->update([
                    'status' => CourseEnrollmentRequest::STATUS_APPROVED,
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            Log::error('Unable to reject course enrollment cancellation.', [
                'course_enrollment_request_id' => $courseEnrollmentRequest->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'The course cancellation could not be rejected. Please try again.');
        }

        if (! $rejected) {
            return back()->with('error', 'Only pending cancellation requests can be rejected.');
        }

        $emailSent = $this->sendStatusEmail($courseEnrollmentRequest, CourseEnrollmentStatusMail::CANCELLATION_REJECTED);

        return back()->with(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'The cancellation was rejected, the student remains enrolled, and an email notification was sent.'
                : 'The cancellation was rejected, but the email notification could not be delivered. Check the mail settings and application log.',
        );
    }

    private function sendStatusEmail(CourseEnrollmentRequest $courseEnrollmentRequest, string $notificationType): bool
    {
        try {
            $courseEnrollmentRequest->loadMissing(['user', 'course']);
            Mail::to($courseEnrollmentRequest->user->email)->send(
                new CourseEnrollmentStatusMail($courseEnrollmentRequest, $notificationType),
            );

            return true;
        } catch (Throwable $exception) {
            Log::error('Unable to send course enrollment status notification.', [
                'course_enrollment_request_id' => $courseEnrollmentRequest->id,
                'notification_type' => $notificationType,
                'exception' => $exception,
            ]);

            return false;
        }
    }
}
