<?php

namespace Database\Factories;

use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PointTransaction>
 */
class PointTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'teacher_id' => User::factory(),
            'amount' => fake()->numberBetween(5, 100),
            'reason' => fake()->sentence(),
        ];
    }
}
