<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
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
            'guardian_id' => Guardian::factory(),
        ];
    }
}
