<?php

namespace App\Mail;

use App\Models\EnrollmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EnrollmentRequest $enrollmentRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Application Approved - Northeastern Cebu Colleges',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-approved',
            with: [
                'student' => $this->enrollmentRequest->user,
                'requirements' => $this->enrollmentRequest->requirements(),
                'studentType' => $this->enrollmentRequest->studentTypeLabel(),
                'organization' => $this->enrollmentRequest->department,
                'academicPeriod' => $this->enrollmentRequest->academicPeriod,
            ],
        );
    }
}
