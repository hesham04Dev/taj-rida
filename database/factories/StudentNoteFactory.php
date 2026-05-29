<?php

namespace Database\Factories;

use App\Models\StudentNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentNote>
 */
class StudentNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'description' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1, 10),
            'date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
        ];
    }
}
