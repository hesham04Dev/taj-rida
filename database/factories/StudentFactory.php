<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'teacher_id' => \App\Models\User::factory(),
            'birthdate' => fake()->date(),
            'points_multiplier' => 1.0,
            'father_name' => fake()->name('male'),
            'father_phone' => fake()->phoneNumber(),
            'access_code' => fake()->unique()->bothify('????####'),
        ];
    }
}
