<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'quiz_id' => Quiz::factory(),
            'status' => SubmissionStatus::Pending,
            'started_at' => now(),
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'completed_at' => now(),
            'duration_seconds' => 120,
        ]);
    }

    public function graded(): static
    {
        return $this->state(fn () => [
            'status' => SubmissionStatus::Graded,
            'completed_at' => now(),
            'total_mark' => 8,
            'duration_seconds' => 120,
        ]);
    }
}
