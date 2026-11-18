<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollmentRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLATION_PENDING = 'cancellation_pending';

    public const STATUS_CANCELLED = 'cancelled';

    public const CANCELLATION_REASONS = [
        'schedule_conflict' => 'Schedule conflict',
        'financial_constraints' => 'Financial constraints',
        'changing_program' => 'Changing academic program',
        'personal_reasons' => 'Personal or family reasons',
        'health_reasons' => 'Health reasons',
        'transportation' => 'Transportation or distance',
        'other' => 'Other',
    ];

    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'cancellation_reason',
        'cancellation_details',
    ];

    public function cancellationReasonLabel(): ?string
    {
        return self::CANCELLATION_REASONS[$this->cancellation_reason] ?? $this->cancellation_reason;
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
