<?php

namespace Tests\Feature\Submissions;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Services\Submissions\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubmissionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_try_creates_submission_and_answers(): void
    {
        $student = User::factory()->student()->create();
        $quiz = Quiz::factory()->create();
        Question::factory()->recording()->create(['quiz_id' => $quiz->id, 'sort_order' => 0]);
        Question::factory()->recording()->create(['quiz_id' => $quiz->id, 'sort_order' => 1]);

        $service = app(SubmissionService::class);
        $submission = $service->startTry($student, $quiz->fresh('questions'));

        $this->assertCount(2, $submission->answers);
        $this->assertNull($submission->completed_at);
    }

    public function test_blocks_second_in_progress_attempt(): void
    {
        $student = User::factory()->student()->create();
        $quiz = Quiz::factory()->create();
        Question::factory()->recording()->create(['quiz_id' => $quiz->id]);

        $service = app(SubmissionService::class);
        $service->startTry($student, $quiz->fresh('questions'));

        $this->expectException(ValidationException::class);
        $service->startTry($student, $quiz->fresh('questions'));
    }
}
