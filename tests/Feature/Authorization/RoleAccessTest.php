<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_access_student_routes(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_teacher_cannot_access_student_routes(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_student_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_teacher_can_access_teacher_routes(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('teacher.submissions.index'))
            ->assertOk();
    }

    public function test_student_cannot_access_teacher_routes(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)
            ->get(route('teacher.submissions.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_teacher_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('teacher.submissions.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_submission_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.submissions.index'))
            ->assertOk();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('manage.quizzes.index'))
            ->assertOk();
    }

    public function test_teacher_cannot_access_admin_routes(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('manage.quizzes.index'))
            ->assertForbidden();
    }

    public function test_student_cannot_access_admin_routes(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)
            ->get(route('manage.quizzes.index'))
            ->assertForbidden();
    }
}
