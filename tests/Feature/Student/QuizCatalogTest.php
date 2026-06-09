<?php

namespace Tests\Feature\Student;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Week;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_quiz_returns_404(): void
    {
        $student = User::factory()->student()->create();
        $quiz = Quiz::factory()->inactive()->create();

        $this->actingAs($student)
            ->get(route('student.quizzes.show', $quiz))
            ->assertNotFound();
    }

    public function test_dashboard_shows_active_weeks(): void
    {
        $student = User::factory()->student()->create();
        Week::factory()->create(['title' => 'Active Week', 'is_active' => true, 'week_number' => 1]);
        Week::factory()->create(['title' => 'Hidden Week', 'is_active' => false, 'week_number' => 2]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Active Week')
            ->assertDontSee('Hidden Week');
    }
}
