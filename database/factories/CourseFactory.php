<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => strtoupper(fake()->lexify('???')) . '-' . fake()->unique()->numberBetween(100, 499),
            'title' => fake()->catchPhrase() . ' Principles',
            'units' => fake()->randomElement([3, 3, 3, 4, 2]),
            'instructor' => 'Prof. ' . fake()->lastName(),
            'prerequisites' => 'None',
            'description' => fake()->paragraph(2),
        ];
    }
}
