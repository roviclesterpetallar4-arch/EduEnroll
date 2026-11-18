<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'student_number' => 'STD-' . fake()->unique()->numberBetween(10000, 99999),
            'year_level' => fake()->numberBetween(1, 4),
            'major' => fake()->randomElement([
                'Computer Science',
                'Information Technology',
                'Software Engineering',
                'Data Science',
                'Computer Engineering',
            ]),
        ];
    }
}
