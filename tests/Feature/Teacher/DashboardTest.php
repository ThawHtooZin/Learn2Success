<?php

namespace Tests\Feature\Teacher;

use App\Enums\SubmissionStatus;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee('grading command center')
            ->assertSee('teacher-workload-chart', false)
            ->assertSee('Priority queue');
    }

    public function test_teacher_dashboard_shows_priority_queue(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->student()->create(['username' => 'priority_student']);
        $quiz = Quiz::factory()->create(['title' => 'Priority Quiz']);

        Submission::factory()->for($student)->for($quiz)->completed()->create([
            'status' => SubmissionStatus::Pending,
            'completed_at' => now()->subHour(),
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee('priority_student')
            ->assertSee('Priority Quiz')
            ->assertSee('Grade');
    }

    public function test_admin_cannot_access_teacher_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_teacher_login_redirects_to_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create(['password' => 'password']);

        $this->post(route('login'), [
            'username' => $teacher->username,
            'password' => 'password',
        ])->assertRedirect(route('teacher.dashboard'));
    }
}
