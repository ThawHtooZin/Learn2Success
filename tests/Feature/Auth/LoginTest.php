<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible_to_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Sign in');
    }

    public function test_login_page_is_accessible_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign in');
    }

    public function test_guest_cannot_access_authenticated_routes(): void
    {
        $this->get(route('student.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->student()->create([
            'username' => 'jane',
            'password' => 'secret-pass',
        ]);

        $this->post(route('login'), [
            'username' => 'jane',
            'password' => 'secret-pass',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->student()->create([
            'username' => 'jane',
            'password' => 'secret-pass',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'username' => 'jane',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_student_is_redirected_to_student_dashboard_after_login(): void
    {
        User::factory()->student()->create([
            'username' => 'student1',
            'password' => 'password',
        ]);

        $this->post(route('login'), [
            'username' => 'student1',
            'password' => 'password',
        ])->assertRedirect(route('student.dashboard'));
    }

    public function test_teacher_is_redirected_to_submissions_after_login(): void
    {
        User::factory()->teacher()->create([
            'username' => 'teacher1',
            'password' => 'password',
        ]);

        $this->post(route('login'), [
            'username' => 'teacher1',
            'password' => 'password',
        ])->assertRedirect(route('teacher.submissions.index'));
    }

    public function test_admin_is_redirected_to_quiz_management_after_login(): void
    {
        User::factory()->admin()->create([
            'username' => 'admin1',
            'password' => 'password',
        ]);

        $this->post(route('login'), [
            'username' => 'admin1',
            'password' => 'password',
        ])->assertRedirect(route('manage.quizzes.index'));
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_home_route(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
