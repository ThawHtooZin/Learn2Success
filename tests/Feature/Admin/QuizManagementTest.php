<?php

namespace Tests\Feature\Admin;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_recording_quiz(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('manage.quizzes.store'), [
                'title' => 'New Speak Quiz',
                'description' => 'Practice speaking',
                'is_active' => true,
                'total_marks' => 10,
                'mark_per_question' => '',
                'questions' => [
                    [
                        'question_text' => 'Introduce yourself.',
                        'question_type' => 'recording',
                    ],
                ],
            ])
            ->assertRedirect(route('manage.quizzes.index'));

        $this->assertDatabaseHas('quizzes', ['title' => 'New Speak Quiz']);
    }

    public function test_recording_quiz_ignores_empty_choice_fields_from_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('manage.quizzes.store'), [
                'title' => 'Speak with stray choices',
                'is_active' => true,
                'total_marks' => 10,
                'questions' => [
                    [
                        'question_text' => 'Say hello.',
                        'question_type' => 'recording',
                        'choices' => [null, null],
                    ],
                    [
                        'question_text' => 'Say goodbye.',
                        'question_type' => 'recording',
                        'choices' => [null, null],
                    ],
                ],
            ])
            ->assertRedirect(route('manage.quizzes.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('quizzes', ['title' => 'Speak with stray choices']);
    }

    public function test_admin_can_create_multiple_choice_quiz(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('manage.quizzes.store'), [
                'title' => 'New MC Quiz',
                'is_active' => true,
                'total_marks' => 6,
                'questions' => [
                    [
                        'question_text' => 'Pick one.',
                        'question_type' => 'multiple_choice',
                        'choices' => ['A', 'B', 'C'],
                        'correct_option_index' => 1,
                    ],
                ],
            ])
            ->assertRedirect(route('manage.quizzes.index'));

        $quiz = Quiz::query()->where('title', 'New MC Quiz')->first();
        $this->assertNotNull($quiz);
        $this->assertCount(1, $quiz->questions);
    }

    public function test_create_quiz_page_renders_question_fields_without_javascript(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('manage.quizzes.create'))
            ->assertOk()
            ->assertSee('data-quiz-form', false)
            ->assertSee('name="questions[0][question_text]"', false)
            ->assertSee('Save quiz');
    }

    public function test_create_quiz_shows_validation_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('manage.quizzes.create'))
            ->post(route('manage.quizzes.store'), [
                'title' => '',
                'total_marks' => 10,
            ])
            ->assertRedirect(route('manage.quizzes.create'))
            ->assertSessionHasErrors(['title', 'questions']);
    }
}
