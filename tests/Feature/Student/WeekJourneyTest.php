<?php

namespace Tests\Feature\Student;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Week;
use App\Enums\QuestionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeekJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_week_journey(): void
    {
        $student = User::factory()->student()->create();
        $week = Week::factory()->create([
            'title' => 'Week 1 — Foundations',
            'week_number' => 1,
            'unlock_after_days' => 0,
            'sort_order' => 1,
        ]);
        Quiz::factory()->create([
            'week_id' => $week->id,
            'title' => 'Week 1 · Quiz 1',
            'sort_order_in_week' => 1,
        ]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Week by week')
            ->assertSee('Week 1 — Foundations');
    }

    public function test_week_detail_shows_quiz_steps(): void
    {
        $student = User::factory()->student()->create();
        $week = Week::factory()->create(['week_number' => 1, 'unlock_after_days' => 0]);
        $quiz = Quiz::factory()->create([
            'week_id' => $week->id,
            'title' => 'Week 1 · Quiz 1',
            'sort_order_in_week' => 1,
            'time_limit_seconds' => 300,
        ]);
        $quiz->questions()->create([
            'question_text' => 'Test question',
            'question_type' => QuestionType::MultipleChoice,
            'meta' => ['choices' => ['a', 'b'], 'correct_option_indexes' => [0]],
            'sort_order' => 0,
        ]);

        $this->actingAs($student)
            ->get(route('student.weeks.show', $week))
            ->assertOk()
            ->assertSee('Week 1 · Quiz 1')
            ->assertSee('5 min limit')
            ->assertSee('Choose');
    }
}
