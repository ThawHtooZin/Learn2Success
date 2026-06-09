# Phase 2, Epic 2 — Admin Week Management

**Status:** ✅ Done  
**Depends:** P2-E1  
**PRD:** Module 14 (Admin Weeks)

## Goal

Admins can **create, edit, list, and delete weeks** and **assign quizzes to weeks** from the admin UI. No more seed-only week setup.

## Create

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Admin/WeekController.php` | Week CRUD |
| `app/Http/Requests/Admin/StoreWeekRequest.php` | Validation |
| `app/Http/Requests/Admin/UpdateWeekRequest.php` | Validation |
| `resources/views/admin/weeks/index.blade.php` | List weeks + quiz counts |
| `resources/views/admin/weeks/create.blade.php` | Create form |
| `resources/views/admin/weeks/edit.blade.php` | Edit + linked quizzes |
| `app/Services/Weeks/WeekQuizAssignmentService.php` | Assign, reorder, remove |
| `app/Http/Controllers/Admin/WeekQuizController.php` | JSON API for quiz panel |
| `resources/js/week-quiz-manager.js` | Alpine drag-drop + add/remove |
| `tests/Feature/Admin/WeekManagementTest.php` | Admin CRUD + auth |

## Modify

| File | Change |
|------|--------|
| `routes/web.php` | `admin.weeks` resource |
| `resources/views/layouts/staff.blade.php` | **Weeks** nav link |
| `resources/views/admin/quizzes/_form.blade.php` | Time limit only (no week fields) |
| `app/Http/Requests/Admin/StoreQuizRequest.php` | `time_limit_seconds` only |
| `resources/views/admin/quizzes/index.blade.php` | Show week column |

## Week fields (admin)

| Field | Notes |
|-------|-------|
| title | Display name |
| week_number | Unique, 1-based |
| unlock_after_days | Days after student registration |
| description | Optional |
| is_active | Hidden from journey when false |
| sort_order | Journey display order |

## Quiz ↔ week linking

**Only in week edit** (`/admin/weeks/{week}/edit`):

- Add quizzes via dropdown + **Add to week**
- Drag-and-drop reorder (desktop) or ↑↓ buttons (mobile)
- Remove quiz from week without deleting the quiz

**Quiz edit** (`/manage/quizzes/{quiz}/edit`): **time limit (seconds)** only — no week or order fields.

## Question types (product rule)

**Only two types:** `multiple_choice` (Choose) and `recording` (Speak).  
`speaking_pattern` removed from enum, admin UI, and validation.

## Acceptance criteria

- [x] Admin sidebar **Weeks** link → `/admin/weeks`
- [x] CRUD weeks with validation
- [x] Week edit shows quizzes assigned to that week
- [x] Quiz form: time limit only (week/order not on quiz edit)
- [x] Student journey reflects admin changes after save
- [x] Only recording + multiple_choice in admin question type dropdown
- [x] Tests pass

## Flow doc

`docs/flows/phase-2-epic-2-admin-week-management-sequence.md`
