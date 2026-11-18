<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const TYPE_INCOMING_FIRST_YEAR = 'incoming_first_year';

    public const TYPE_TRANSFEREE = 'transferee';

    protected $fillable = [
        'user_id',
        'contact_number',
        'student_type',
        'status',
        'academic_period_id',
        'department_id',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function studentTypeLabel(): string
    {
        return match ($this->student_type) {
            self::TYPE_TRANSFEREE => 'Transferee',
            default => 'Incoming 1st Year',
        };
    }

    public function requirements(): array
    {
        $requirements = $this->student_type === self::TYPE_TRANSFEREE
            ? ['Transcript of Records (TOR) for evaluation purposes']
            : ['Report Card (Original)'];

        return [
            ...$requirements,
            'Certificate of Good Moral Character (Original)',
            'NSO/PSA Birth Certificate (Original)',
            '2x2 Picture with White Background (2 pcs)',
        ];
    }
}
