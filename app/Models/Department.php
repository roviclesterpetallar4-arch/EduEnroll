<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'head',
        'description',
    ];

    /**
     * One-to-Many: Department has many Courses.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(StudentProfile::class, Course::class);
    }

}
