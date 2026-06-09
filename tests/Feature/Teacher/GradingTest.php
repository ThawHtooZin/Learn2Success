<?php

namespace Tests\Feature\Teacher;

use App\Enums\SubmissionStatus;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingTest extends TestCase
{
    use RefreshDatabase;

    public function test_grade_form_shows_quiz_total_and_max_per_question(): void
    {
        $teacher = User::factory()->teacher()->create();
        $quiz = Quiz::factory()->create([
            'title' => 'Speak Up Quiz',
            'description' => 'Two recording questions.',
            'total_marks' => 10,
            'mark_per_question' => 5,
        ]);
        $question = Question::factory()->recording()->create(['quiz_id' => $quiz->id]);
        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $question->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Quiz total: 10 marks')
            ->assertSee('Quiz details (read-only)')
            ->assertSee('Mark per question (max)')
            ->assertSee('5.00')
            ->assertSee('Speaking answer — grade out of 5.00 marks max.');
    }

    public function test_teacher_cannot_exceed_per_question_max(): void
    {
        $teacher = User::factory()->teacher()->create();
        $quiz = Quiz::factory()->create(['total_marks' => 10, 'mark_per_question' => 5]);
        $question = Question::factory()->recording()->create(['quiz_id' => $quiz->id]);
        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        $answer = Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $question->id,
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.submissions.update', $submission), [
                'answers' => [
                    $answer->id => ['mark' => 6, 'teacher_feedback' => null],
                ],
                'teacher_feedback' => 'Too high',
            ])
            ->assertSessionHasErrors(['answers.'.$answer->id.'.mark']);

        $this->assertSame(SubmissionStatus::Pending, $submission->fresh()->status);
    }

    public function test_teacher_cannot_exceed_quiz_total(): void
    {
        $teacher = User::factory()->teacher()->create();
        $quiz = Quiz::factory()->create(['total_marks' => 5, 'mark_per_question' => 3]);
        $q1 = Question::factory()->recording()->create(['quiz_id' => $quiz->id, 'sort_order' => 0]);
        $q2 = Question::factory()->recording()->create(['quiz_id' => $quiz->id, 'sort_order' => 1]);
        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        $a1 = Answer::factory()->create(['submission_id' => $submission->id, 'question_id' => $q1->id]);
        $a2 = Answer::factory()->create(['submission_id' => $submission->id, 'question_id' => $q2->id]);

        $this->actingAs($teacher)
            ->put(route('teacher.submissions.update', $submission), [
                'answers' => [
                    $a1->id => ['mark' => 3, 'teacher_feedback' => null],
                    $a2->id => ['mark' => 3, 'teacher_feedback' => null],
                ],
            ])
            ->assertSessionHasErrors('total_mark');

        $this->assertSame(SubmissionStatus::Pending, $submission->fresh()->status);
    }

    public function test_teacher_can_save_valid_grading(): void
    {
        $teacher = User::factory()->teacher()->create();
        $quiz = Quiz::factory()->create(['total_marks' => 10, 'mark_per_question' => 5]);
        $question = Question::factory()->recording()->create(['quiz_id' => $quiz->id]);
        $submission = Submission::factory()->completed()->create(['quiz_id' => $quiz->id]);
        $answer = Answer::factory()->create([
            'submission_id' => $submission->id,
            'question_id' => $question->id,
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.submissions.update', $submission), [
                'answers' => [
                    $answer->id => ['mark' => 4.5, 'teacher_feedback' => 'Good effort'],
                ],
                'teacher_feedback' => 'Well done',
            ])
            ->assertRedirect(route('teacher.submissions.show', $submission));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::Graded, $submission->status);
        $this->assertEquals(4.5, (float) $submission->total_mark);
        $this->assertEquals(4.5, (float) $answer->fresh()->mark);
    }
}
