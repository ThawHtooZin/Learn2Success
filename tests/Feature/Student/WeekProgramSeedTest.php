<?php

namespace Tests\Feature\Student;

use App\Enums\QuestionType;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Week;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\QuizSeeder;
use Database\Seeders\WeekSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeekProgramSeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            WeekSeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
        ]);
    }

    public function test_program_has_four_weeks_with_four_quizzes_each(): void
    {
        $this->assertSame(4, Week::query()->count());
        $this->assertSame(16, Quiz::query()->whereNotNull('week_id')->count());
    }

    public function test_speak_up_quiz_has_only_recording_questions(): void
    {
        $quiz = $this->programQuiz(1, 3);

        $this->assertStringContainsString('Speak Up', $quiz->title);
        $this->assertCount(2, $quiz->questions);
        $this->assertTrue(
            $quiz->questions->every(fn ($q) => $q->question_type === QuestionType::Recording),
        );
    }

    public function test_choose_and_speak_quiz_has_both_question_types(): void
    {
        $quiz = $this->programQuiz(1, 4);

        $this->assertStringContainsString('Choose & Speak', $quiz->title);

        $types = $quiz->distinctQuestionTypes();

        $this->assertContains(QuestionType::MultipleChoice, $types);
        $this->assertContains(QuestionType::Recording, $types);
        $this->assertCount(2, $types);
    }

    public function test_quiz_detail_shows_question_type_tags(): void
    {
        $student = User::factory()->student()->create();
        $quiz = $this->programQuiz(1, 3);

        $this->actingAs($student)
            ->get(route('student.quizzes.show', $quiz))
            ->assertOk()
            ->assertSee('Speak');
    }

    public function test_play_view_includes_recording_questions(): void
    {
        $student = User::factory()->student()->create();
        $quiz = $this->programQuiz(1, 3);

        $this->actingAs($student)
            ->get(route('student.quizzes.play', ['quiz' => $quiz, 'start' => 1]))
            ->assertOk()
            ->assertSee('Speak');
    }

    private function programQuiz(int $weekNumber, int $slot): Quiz
    {
        return Quiz::query()
            ->whereHas('week', fn ($q) => $q->where('week_number', $weekNumber))
            ->where('sort_order_in_week', $slot - 1)
            ->with('questions')
            ->firstOrFail();
    }
}
