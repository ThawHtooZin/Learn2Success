# Phase 2, Epic 1 — Week Management & Gamification Journey

**Status:** ✅ Done  
**Depends:** Phase 1 complete  
**PRD extension:** Week-based unlock schedule + gamified student journey

## Goal

Replace the flat student quiz list with a **Coddy-style step-by-step journey**: weeks unlock on a day schedule from registration; each week contains quizzes with time limits; students retake anytime once unlocked.

**Not in scope:** energy/lives, admin week CRUD, streaks/badges.

---

## Create

### Database

| File | Purpose |
|------|---------|
| `database/migrations/*_create_weeks_table.php` | `weeks` table |
| `database/migrations/*_add_week_fields_to_quizzes_table.php` | `week_id`, `time_limit_seconds`, `sort_order_in_week` |

### Models & factories

| File | Purpose |
|------|---------|
| `app/Models/Week.php` | Week model |
| `database/factories/WeekFactory.php` | Test factory |

### Services

| File | Purpose |
|------|---------|
| `app/Services/Weeks/WeekUnlockService.php` | Unlock logic from `user.created_at` |
| `app/Services/Weeks/WeekJourneyService.php` | Journey map + quiz step states |

### HTTP

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Student/WeekController.php` | Week detail (quiz path) |

### Views & components

| File | Purpose |
|------|---------|
| `resources/views/student/dashboard.blade.php` | Week journey map (replace card list) |
| `resources/views/student/weeks/show.blade.php` | Quiz step path inside week |
| `resources/views/components/journey-week-node.blade.php` | Week node on path |
| `resources/views/components/journey-quiz-step.blade.php` | Quiz step node |

### Frontend

| File | Purpose |
|------|---------|
| `resources/js/student-journey.js` | Path animations, active node helpers |
| `resources/css/app.css` | Journey path, page transitions, node animations |

### Seeders

| File | Purpose |
|------|---------|
| `database/seeders/WeekSeeder.php` | 4 weeks (0/7/14/21 day unlock) |
| Update `QuizSeeder.php` | 16 program quizzes (4 × 4 weeks) |
| Update `QuestionSeeder.php` | 3 MC questions per program quiz |
| Update `UserSeeder.php` | `student_allweeks`, `student_jan1` test accounts |

### Tests

| File | Purpose |
|------|---------|
| `tests/Feature/Student/WeekUnlockTest.php` | Unlock rules + 403 on locked week/quiz |
| `tests/Feature/Student/WeekJourneyTest.php` | Journey map + week detail render |

---

## Modify

| File | Change |
|------|--------|
| `app/Models/Quiz.php` | `week_id`, `time_limit_seconds`, `sort_order_in_week` |
| `app/Http/Controllers/Student/DashboardController.php` | Use `WeekJourneyService` |
| `app/Http/Controllers/Student/QuizController.php` | Week unlock guard on show/play |
| `routes/web.php` | `student.weeks.show` |
| `resources/js/app.js` | Import `student-journey.js` |
| `resources/js/quiz-play.js` | Quiz time-limit countdown + auto-complete |
| `resources/views/student/quizzes/play.blade.php` | Timer UI |
| `resources/views/layouts/student.blade.php` | Page transition wrapper |
| `database/seeders/DatabaseSeeder.php` | Call `WeekSeeder` before quizzes |

---

## Acceptance criteria

- [x] 4 weeks seeded; 4 quizzes each with `time_limit_seconds`
- [x] Week 1 unlocks on registration day; week 2 after 7 days, etc.
- [x] Locked weeks/quizzes return 403 or show locked state on map
- [x] Unlocked quizzes: take + retake (multi-attempt preserved)
- [x] Play UI shows countdown when time limit set
- [x] Student dashboard = vertical week journey (not card grid)
- [x] Week detail = numbered quiz steps (Coddy-style path)
- [x] Page slide transitions between journey screens
- [x] No energy feature
- [x] Pest tests pass
- [x] Flow doc created

## Flow doc to create

`docs/flows/phase-2-epic-1-week-gamification-sequence.md`
