<?php

namespace Tests\Feature\Student;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Week;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WeekUnlockTest extends TestCase
{
    use RefreshDatabase;

    public function test_week_one_unlocks_on_registration_day(): void
    {
        Carbon::setTestNow('2026-06-08 12:00:00');

        $student = User::factory()->student()->create(['created_at' => '2026-06-08 08:00:00']);
        $week = Week::factory()->create(['week_number' => 1, 'unlock_after_days' => 0]);

        $this->actingAs($student)
            ->get(route('student.weeks.show', $week))
            ->assertOk();
    }

    public function test_week_two_locked_before_seven_days(): void
    {
        Carbon::setTestNow('2026-06-08 12:00:00');

        $student = User::factory()->student()->create(['created_at' => '2026-06-08 08:00:00']);
        $week = Week::factory()->create(['week_number' => 2, 'unlock_after_days' => 7]);

        $this->actingAs($student)
            ->get(route('student.weeks.show', $week))
            ->assertForbidden();
    }

    public function test_week_two_unlocks_after_seven_days(): void
    {
        Carbon::setTestNow('2026-06-15 12:00:00');

        $student = User::factory()->student()->create(['created_at' => '2026-06-08 08:00:00']);
        $week = Week::factory()->create(['week_number' => 2, 'unlock_after_days' => 7]);

        $this->actingAs($student)
            ->get(route('student.weeks.show', $week))
            ->assertOk();
    }

    public function test_locked_week_quiz_returns_403(): void
    {
        Carbon::setTestNow('2026-06-08 12:00:00');

        $student = User::factory()->student()->create(['created_at' => '2026-06-08 08:00:00']);
        $week = Week::factory()->create(['week_number' => 2, 'unlock_after_days' => 7]);
        $quiz = Quiz::factory()->create(['week_id' => $week->id, 'is_active' => true]);

        $this->actingAs($student)
            ->get(route('student.quizzes.show', $quiz))
            ->assertForbidden();
    }

    public function test_legacy_quiz_without_week_stays_accessible(): void
    {
        $student = User::factory()->student()->create();
        $quiz = Quiz::factory()->create(['week_id' => null, 'is_active' => true]);

        $this->actingAs($student)
            ->get(route('student.quizzes.show', $quiz))
            ->assertOk();
    }
}
