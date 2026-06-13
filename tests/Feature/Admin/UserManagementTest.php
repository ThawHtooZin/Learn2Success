<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'username' => 'newuser',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['username' => 'newuser']);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();
    }

    public function test_admin_user_index_supports_search_and_role_filter(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->student()->create(['username' => 'findme_student']);
        User::factory()->teacher()->create(['username' => 'findme_teacher']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['q' => 'findme', 'role' => 'student']))
            ->assertOk()
            ->assertSee('findme_student')
            ->assertDontSee('findme_teacher')
            ->assertSee('data-table-toolbar', false);
    }
}
