<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'College of Computer Studies',
                'College of Engineering',
                'College of Business Administration',
                'College of Arts and Sciences',
                'College of Education',
            ]),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'head' => 'Dr. ' . fake()->name(),
            'description' => fake()->paragraph(2),
        ];
    }
}
