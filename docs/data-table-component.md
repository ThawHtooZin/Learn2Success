# Data table component — search, sort, filter, pagination

Staff listing pages (admin + teacher) use a shared **data table** pattern: server-side search, column sorting, filters, and pagination with query-string state.

---

## When to use

Use `<x-data-table>` on any **paginated staff table** (Users, Quizzes, Weeks, Submissions). Small fixed lists (e.g. dashboard “recent activity”) do not need it.

---

## Architecture

```
GET /admin/users?q=...&role=...&sort=...&direction=...&per_page=...
  → Controller builds TableQuery
  → applySearch → apply filters → applySort → paginate()
  → Blade: toolbar + sort headers + footer
```

**Backend:** `App\Support\Tables\TableQuery`  
**Views:** `resources/views/components/data-table/*`

---

## Components

| Component | Purpose |
|-----------|---------|
| `<x-data-table>` | Card wrapper, table, optional toolbar/footer slots |
| `<x-data-table.toolbar>` | Search, filter slot, per-page, Apply, Clear |
| `<x-data-table.filter-select>` | Dropdown filter (auto-submit on change) |
| `<x-data-table.sort-header>` | Clickable column header (↑ ↓ ↕) |
| `<x-data-table.footer>` | “Showing X–Y of Z” + pagination |

Pagination view: `resources/views/vendor/pagination/data-table.blade.php`

---

## Controller pattern

```php
use App\Support\Tables\TableQuery;
use Illuminate\Http\Request;

public function index(Request $request): View
{
    $tableQuery = TableQuery::make(
        $request,
        sortableColumns: ['username', 'role'],
        defaultSort: 'username',
        defaultDirection: 'asc',
        defaultPerPage: 15,
    );

    $query = User::query();
    $tableQuery->applySearch($query, ['username']);

    if ($role = $tableQuery->filter('role')) {
        $query->where('role', $role);
    }

    $tableQuery->applySort($query);
    $users = $tableQuery->paginate($query);

    return view('admin.users.index', compact('users', 'tableQuery'));
}
```

### Relation search

```php
$tableQuery->applySearch($query, ['user.username', 'quiz.title']);
```

### Custom sort (joins)

```php
$tableQuery->applySort($query, [
    'week_number' => function ($builder, string $direction): void {
        $builder
            ->leftJoin('weeks', 'weeks.id', '=', 'quizzes.week_id')
            ->orderBy('weeks.week_number', $direction)
            ->select('quizzes.*');
    },
]);
```

---

## Query parameters

| Param | Purpose |
|-------|---------|
| `q` | Search term |
| `sort` | Column key (must be in `$sortableColumns`) |
| `direction` | `asc` or `desc` |
| `per_page` | `10`, `15`, `25`, or `50` |
| `*` | Custom filters per table (`role`, `active`, `week`, `filter`, …) |

All params are preserved in pagination and sort links (`withQueryString()`).

---

## Blade example

```blade
<x-data-table>
    <x-slot:toolbar>
        <x-data-table.toolbar :table="$tableQuery" search-placeholder="Search username…">
            <x-slot:filters>
                <x-data-table.filter-select
                    label="Role"
                    name="role"
                    :options="['' => 'All roles', 'student' => 'Student']"
                />
            </x-slot:filters>
        </x-data-table.toolbar>
    </x-slot:toolbar>

    <x-slot:head>
        <tr>
            <x-data-table.sort-header column="username" label="Username" :table="$tableQuery" />
        </tr>
    </x-slot:head>

    @forelse ($users as $user)
        {{-- rows --}}
    @empty
        <tr><td colspan="3">No results…</td></tr>
    @endforelse

    <x-slot:footer>
        <x-data-table.footer :paginator="$users" />
    </x-slot:footer>
</x-data-table>
```

### Default filter (Clear button)

If a filter default should not count as “active”:

```blade
<x-data-table.toolbar :clear-ignore="['filter' => 'ready']" …>
```

---

## Pages using data tables

| Page | Route | Search | Filters | Sortable columns |
|------|-------|--------|---------|------------------|
| Users | `admin.users.index` | username | role | username, role |
| Quizzes | `manage.quizzes.index` | title, week title | active, week assignment | title, week_number, questions_count, is_active, id |
| Weeks | `admin.weeks.index` | title | active | title, week_number, unlock_after_days, quizzes_count, is_active, sort_order |
| Submissions | `teacher.submissions.index` | student, quiz | status (ready / in progress / graded / all) | student, quiz, created_at |

---

## Tests

```bash
php artisan test --filter=TableQueryTest
php artisan test --filter=UserManagementTest
```

---

## Manual QA

1. Open **Users** → search partial username → Apply.
2. Click **Username** header twice → asc then desc; arrow indicator updates.
3. Change **Per page** to 10 → list shrinks; footer shows range.
4. Set filters → **Clear** resets to unfiltered list.
5. Repeat on Quizzes, Weeks, Teacher Submissions.
