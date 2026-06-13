<?php

namespace Tests\Unit\Support;

use App\Models\User;
use App\Support\Tables\TableQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TableQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_users_by_username(): void
    {
        User::factory()->student()->create(['username' => 'alpha_student']);
        User::factory()->student()->create(['username' => 'beta_student']);

        $request = Request::create('/admin/users', 'GET', ['q' => 'alpha']);
        $tableQuery = TableQuery::make($request, ['username', 'role'], 'username', 'asc');

        $query = User::query();
        $tableQuery->applySearch($query, ['username']);

        $this->assertSame(1, $query->count());
        $this->assertSame('alpha_student', $query->first()->username);
    }

    public function test_sort_toggles_direction_in_url(): void
    {
        $request = Request::create('/admin/users', 'GET', ['sort' => 'username', 'direction' => 'asc']);
        $tableQuery = TableQuery::make($request, ['username', 'role'], 'username', 'asc');

        $this->assertStringContainsString('direction=desc', $tableQuery->sortUrl('username'));
    }

    public function test_has_active_filters_ignores_default_values(): void
    {
        $request = Request::create('/teacher/submissions', 'GET', ['filter' => 'ready']);
        $tableQuery = TableQuery::make($request, ['student'], 'created_at', 'desc');

        $this->assertFalse($tableQuery->hasActiveFilters(ignoreValues: ['filter' => 'ready']));
        $this->assertTrue($tableQuery->hasActiveFilters());
    }
}
