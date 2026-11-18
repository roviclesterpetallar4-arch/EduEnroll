<?php

namespace App\Mail;

use App\Models\CourseEnrollmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseEnrollmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public const ENROLLMENT_APPROVED = 'enrollment_approved';

    public const ENROLLMENT_REJECTED = 'enrollment_rejected';

    public const CANCELLATION_APPROVED = 'cancellation_approved';

    public const CANCELLATION_REJECTED = 'cancellation_rejected';

    public function __construct(
        public CourseEnrollmentRequest $courseEnrollmentRequest,
        public string $notificationType,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: match ($this->notificationType) {
                self::ENROLLMENT_APPROVED => 'Course Enrollment Accepted - Northeastern Cebu Colleges',
                self::ENROLLMENT_REJECTED => 'Course Enrollment Request Update - Northeastern Cebu Colleges',
                self::CANCELLATION_APPROVED => 'Course Cancellation Approved - Northeastern Cebu Colleges',
                self::CANCELLATION_REJECTED => 'Course Cancellation Update - Northeastern Cebu Colleges',
                default => throw new \InvalidArgumentException('Unknown course enrollment notification type.'),
            },
        );
    }

    public function content(): Content
    {
        [$heading, $bodyText] = match ($this->notificationType) {
            self::ENROLLMENT_APPROVED => [
                'Course Enrollment Accepted',
                'Your request has been accepted. Please wait for NCC to contact you with your examination and interview schedule.',
            ],
            self::ENROLLMENT_REJECTED => [
                'Course Enrollment Request Update',
                'Your request was not approved. You may contact NCC administration if you need more information.',
            ],
            self::CANCELLATION_APPROVED => [
                'Course Cancellation Approved',
                'Your course cancellation has been approved. You may now submit a request for another course.',
            ],
            self::CANCELLATION_REJECTED => [
                'Course Cancellation Request Update',
                'Your cancellation request was not approved, and you remain enrolled in this course.',
            ],
            default => throw new \InvalidArgumentException('Unknown course enrollment notification type.'),
        };

        return new Content(
            view: 'emails.course-enrollment-status',
            with: [
                'student' => $this->courseEnrollmentRequest->user,
                'course' => $this->courseEnrollmentRequest->course,
                'heading' => $heading,
                'bodyText' => $bodyText,
            ],
        );
    }
}
