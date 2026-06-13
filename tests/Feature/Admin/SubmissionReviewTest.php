<?php

namespace Tests\Feature\Admin;

use App\Enums\SubmissionStatus;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_submission_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.submissions.index'))
            ->assertOk()
            ->assertSee('Submissions');
    }

    public function test_admin_can_view_submission_details_read_only(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = Quiz::factory()->create(['total_marks' => 10, 'mark_per_question' => 5]);
        $question = Question::factory()->recording()->create(['quiz_id' => $quiz->id]);
        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $question->id,
            'mark' => 4,
            'teacher_feedback' => 'Nice pronunciation',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.show', $submission))
            ->assertOk()
            ->assertSee('View only — teachers grade submissions')
            ->assertSee('4.00')
            ->assertSee('Nice pronunciation')
            ->assertDontSee('Save grading')
            ->assertDontSee('name="answers');
    }

    public function test_admin_cannot_save_grading_via_teacher_route(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = Quiz::factory()->create(['total_marks' => 10, 'mark_per_question' => 5]);
        $question = Question::factory()->recording()->create(['quiz_id' => $quiz->id]);
        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        $answer = Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $question->id,
        ]);

        $this->actingAs($admin)
            ->put(route('teacher.submissions.update', $submission), [
                'answers' => [
                    $answer->id => ['mark' => 5, 'teacher_feedback' => null],
                ],
            ])
            ->assertForbidden();

        $this->assertSame(SubmissionStatus::Pending, $submission->fresh()->status);
        $this->assertNull($answer->fresh()->mark);
    }

    public function test_teacher_cannot_access_admin_submission_routes(): void
    {
        $teacher = User::factory()->teacher()->create();
        $submission = Submission::factory()->completed()->create();

        $this->actingAs($teacher)
            ->get(route('admin.submissions.index'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->get(route('admin.submissions.show', $submission))
            ->assertForbidden();
    }
}
