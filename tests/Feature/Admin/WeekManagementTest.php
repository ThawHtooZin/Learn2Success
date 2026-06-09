<?php

namespace Tests\Feature\Admin;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Week;
use Database\Seeders\QuizSeeder;
use Database\Seeders\WeekSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeekManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_week(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.weeks.store'), [
                'title' => 'Foundations',
                'week_number' => 5,
                'unlock_after_days' => 28,
                'description' => 'Fifth week content',
                'is_active' => true,
                'sort_order' => 4,
            ]);

        $week = Week::query()->where('week_number', 5)->firstOrFail();
        $response->assertRedirect(route('admin.weeks.edit', $week));

        $this->assertDatabaseHas('weeks', [
            'title' => 'Foundations',
            'week_number' => 5,
            'unlock_after_days' => 28,
        ]);
    }

    public function test_admin_can_update_week(): void
    {
        $admin = User::factory()->admin()->create();
        $week = Week::factory()->create(['title' => 'Old title']);

        $this->actingAs($admin)
            ->put(route('admin.weeks.update', $week), [
                'title' => 'New title',
                'week_number' => $week->week_number,
                'unlock_after_days' => 14,
                'description' => 'Updated',
                'is_active' => true,
                'sort_order' => 2,
            ])
            ->assertRedirect(route('admin.weeks.edit', $week));

        $this->assertDatabaseHas('weeks', [
            'id' => $week->id,
            'title' => 'New title',
            'unlock_after_days' => 14,
        ]);
    }

    public function test_admin_can_delete_week_and_unassign_quizzes(): void
    {
        $admin = User::factory()->admin()->create();
        $week = Week::factory()->create();
        $quiz = Quiz::factory()->create(['week_id' => $week->id]);

        $this->actingAs($admin)
            ->delete(route('admin.weeks.destroy', $week))
            ->assertRedirect(route('admin.weeks.index'));

        $this->assertDatabaseMissing('weeks', ['id' => $week->id]);
        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'week_id' => null,
        ]);
    }

    public function test_student_cannot_access_week_admin(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)
            ->get(route('admin.weeks.index'))
            ->assertForbidden();
    }

    public function test_admin_week_index_lists_weeks(): void
    {
        $admin = User::factory()->admin()->create();
        Week::factory()->create(['title' => 'Week Alpha']);

        $this->actingAs($admin)
            ->get(route('admin.weeks.index'))
            ->assertOk()
            ->assertSee('Week Alpha');
    }

    public function test_admin_week_edit_shows_seeded_quizzes(): void
    {
        $this->seed([
            WeekSeeder::class,
            QuizSeeder::class,
        ]);

        $admin = User::factory()->admin()->create();
        $week = Week::query()->where('week_number', 2)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.weeks.edit', $week))
            ->assertOk()
            ->assertSee('Week 2 · Quick Choose')
            ->assertSee('Week 2 · Speak Up')
            ->assertSee('4 quizzes');
    }

    public function test_admin_can_assign_quiz_to_week(): void
    {
        $admin = User::factory()->admin()->create();
        $week = Week::factory()->create();
        $quiz = Quiz::factory()->create(['week_id' => null]);

        $this->actingAs($admin)
            ->postJson(route('admin.weeks.quizzes.store', $week), [
                'quiz_id' => $quiz->id,
            ])
            ->assertOk()
            ->assertJsonPath('quiz.id', $quiz->id);

        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'week_id' => $week->id,
            'sort_order_in_week' => 0,
        ]);
    }

    public function test_admin_can_reorder_week_quizzes(): void
    {
        $admin = User::factory()->admin()->create();
        $week = Week::factory()->create();
        $first = Quiz::factory()->create(['week_id' => $week->id, 'sort_order_in_week' => 0]);
        $second = Quiz::factory()->create(['week_id' => $week->id, 'sort_order_in_week' => 1]);

        $this->actingAs($admin)
            ->putJson(route('admin.weeks.quizzes.reorder', $week), [
                'quiz_ids' => [$second->id, $first->id],
            ])
            ->assertOk();

        $this->assertDatabaseHas('quizzes', ['id' => $second->id, 'sort_order_in_week' => 0]);
        $this->assertDatabaseHas('quizzes', ['id' => $first->id, 'sort_order_in_week' => 1]);
    }

    public function test_admin_can_remove_quiz_from_week(): void
    {
        $admin = User::factory()->admin()->create();
        $week = Week::factory()->create();
        $quiz = Quiz::factory()->create(['week_id' => $week->id, 'sort_order_in_week' => 0]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.weeks.quizzes.destroy', [$week, $quiz]))
            ->assertOk();

        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'week_id' => null,
        ]);
    }
}
