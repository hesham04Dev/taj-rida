<?php

namespace Database\Factories;

use App\Models\Memorization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Memorization>
 */
class MemorizationFactory extends Factory
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
            'sura_id' => \App\Models\Sura::factory(),
            'memorized_pages' => fake()->randomFloat(1, 0, 20),
            'memorization_degree' => null,
            'memorization_repetition' => fake()->numberBetween(0, 5),
            'revision_degree' => null,
            'revision_repetition' => fake()->numberBetween(0, 5),
            'is_need_rememorisation' => false,
            'is_need_revision' => false,
            'need_from_page' => null,
            'need_to_page' => null,
        ];
    }
}
