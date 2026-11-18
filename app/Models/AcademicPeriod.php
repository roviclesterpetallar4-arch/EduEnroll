<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    protected $fillable = [
        'year_start',
        'year_end',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year_start' => 'integer',
            'year_end' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function label(): string
    {
        return "AY {$this->year_start}-{$this->year_end}";
    }
}
