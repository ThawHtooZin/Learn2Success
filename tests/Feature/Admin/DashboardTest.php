<?php

namespace Tests\Feature\Admin;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->student()->count(2)->create();
        User::factory()->teacher()->create();
        Quiz::factory()->count(3)->create(['is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Program overview')
            ->assertSee('Students')
            ->assertSee('admin-submissions-chart', false)
            ->assertSee('data-chart-id="admin-status-chart"', false);
    }

    public function test_admin_dashboard_shows_pending_grade_count(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $quiz = Quiz::factory()->create();

        Submission::factory()->for($student)->for($quiz)->completed()->create([
            'status' => SubmissionStatus::Pending,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pending grades')
            ->assertSee('1');
    }

    public function test_teacher_cannot_access_admin_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'password']);

        $this->post(route('login'), [
            'username' => $admin->username,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }
}
