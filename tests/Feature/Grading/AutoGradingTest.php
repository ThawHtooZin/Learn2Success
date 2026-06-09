<?php

namespace Tests\Feature\Grading;

use App\Enums\SubmissionStatus;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use App\Services\Grading\AutoGradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoGradingTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_mc_quiz_is_auto_graded(): void
    {
        $quiz = Quiz::factory()->create(['total_marks' => 4, 'mark_per_question' => 2]);
        $q1 = Question::factory()->multipleChoice()->create(['quiz_id' => $quiz->id, 'sort_order' => 0]);
        $q2 = Question::factory()->multipleChoice()->create(['quiz_id' => $quiz->id, 'sort_order' => 1]);

        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $q1->id,
            'selected_options' => [0],
        ]);
        Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $q2->id,
            'selected_options' => [0],
        ]);

        $submission->load(['answers.question', 'quiz.questions']);
        app(AutoGradingService::class)->grade($submission);

        $submission->refresh();
        $this->assertSame(SubmissionStatus::Graded, $submission->status);
        $this->assertEquals(4, (float) $submission->total_mark);
    }
}
