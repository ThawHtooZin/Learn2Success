<?php

namespace Tests\Feature\Student;

use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTimeLimitCompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_accepts_time_expired_with_unanswered_questions(): void
    {
        $student = User::factory()->student()->create();
        $quiz = Quiz::factory()->create(['time_limit_seconds' => 180]);

        Question::factory()->multipleChoice()->create([
            'quiz_id' => $quiz->id,
            'sort_order' => 0,
        ]);

        $submission = Submission::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'completed_at' => null,
        ]);

        $submission->answers()->create([
            'question_id' => $quiz->questions()->first()->id,
        ]);

        $this->actingAs($student)
            ->postJson(route('student.submissions.complete', $submission), [
                'duration_seconds' => 180,
                'time_expired' => true,
            ])
            ->assertOk()
            ->assertJsonStructure(['redirect']);

        $submission->refresh();
        $this->assertNotNull($submission->completed_at);
    }
}
