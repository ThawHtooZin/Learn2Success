# Phase 2, Epic 5 — Staff Data Tables

**Status:** ✅ Done  
**Depends:** Phase 1 staff CRUD pages  
**PRD:** Module 17 (Staff Data Tables)

---

## Goal

Replace plain paginated tables on staff listing pages with a reusable **search, sort, filter, and pagination** pattern.

---

## Why / What / Result

| | |
|---|---|
| **Why** | Users, quizzes, weeks, and submissions lists grew; staff needed quick search and column sorting without custom code per page. |
| **What** | `TableQuery` helper + Blade components applied to admin Users, Quizzes, Weeks, and teacher/admin Submissions index. |
| **Result** | Consistent toolbar (search, filters, per-page), sortable headers, query-string state preserved on pagination. |

---

## Architecture

```
GET /admin/users?q=...&sort=...&filter=...
  → Controller builds TableQuery
  → applySearch → filters → applySort → paginate()
  → <x-data-table> Blade components
```

**Backend:** `App\Support\Tables\TableQuery`  
**Views:** `resources/views/components/data-table/*`  
**Guide:** [docs/data-table-component.md](../data-table-component.md)

---

## Pages using data tables

| Page | Controller |
|------|------------|
| Admin users | `Admin\UserController@index` |
| Admin quizzes | `Admin\QuizController@index` |
| Admin weeks | `Admin\WeekController@index` |
| Teacher submissions | `Teacher\SubmissionController@index` |
| Admin submissions | `Admin\SubmissionController@index` |

Dashboard “recent activity” and priority queue lists stay as fixed small tables (no data-table component).

---

## Tests

| File | Covers |
|------|--------|
| `tests/Unit/Support/TableQueryTest.php` | Search, sort, filter, pagination params |
| `tests/Feature/Admin/UserManagementTest.php` | Search/sort on users index |
