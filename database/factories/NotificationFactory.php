<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory(),
            'student_id' => null,
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
        ];
    }

    /**
     * Target a specific student.
     */
    public function forStudent(Student $student): static
    {
        return $this->state(['student_id' => $student->id]);
    }
}
