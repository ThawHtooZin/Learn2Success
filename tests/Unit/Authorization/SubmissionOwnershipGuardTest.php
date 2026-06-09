<?php

namespace Tests\Unit\Authorization;

use App\Models\User;
use App\Services\Authorization\SubmissionOwnershipGuard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionOwnershipGuardTest extends TestCase
{
    use RefreshDatabase;

    private SubmissionOwnershipGuard $guard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guard = new SubmissionOwnershipGuard;
    }

    public function test_allows_student_to_access_own_submission(): void
    {
        $student = User::factory()->student()->create();

        $this->guard->authorize($student, $student->id);

        $this->assertTrue(true);
    }

    public function test_denies_student_access_to_another_students_submission(): void
    {
        $student = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();

        $this->expectException(AuthorizationException::class);

        $this->guard->authorize($student, $otherStudent->id);
    }

    public function test_denies_teacher_from_student_submission_apis(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->student()->create();

        $this->expectException(AuthorizationException::class);

        $this->guard->authorize($teacher, $student->id);
    }

    public function test_denies_admin_from_student_submission_apis(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();

        $this->expectException(AuthorizationException::class);

        $this->guard->authorize($admin, $student->id);
    }
}
