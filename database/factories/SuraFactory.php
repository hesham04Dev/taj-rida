<?php

namespace Database\Factories;

use App\Models\Sura;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sura>
 */
class SuraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fromPage = fake()->numberBetween(1, 600);
        $toPage = $fromPage + fake()->numberBetween(0, 10);

        return [
            'name' => fake()->unique()->word(),
            'pages_count' => $toPage - $fromPage + 1,
            'from_page' => $fromPage,
            'to_page' => $toPage,
        ];
    }
}
