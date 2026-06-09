# Implementation Status — Learn2Success

**Last updated:** Phase 2, Epic 2 complete  
**Docs index:** [docs/README.md](README.md)

## Epic summary

| Epic | Name | Status |
|------|------|--------|
| E1–E12 | Phase 1 modules | ✅ Done |
| **P2-E1** | Week Management & Gamification Journey | ✅ Done |
| **P2-E2** | Admin Week Management | ✅ Done |

Details: [phase-1-technical-design-and-tasks.md](phase-1-technical-design-and-tasks.md) · [phase-2-technical-design-and-tasks.md](phase-2-technical-design-and-tasks.md)

## Screens

| # | Screen | Status |
|---|--------|--------|
| 1 | Marketing home | ✅ |
| 2 | Sign in | ✅ |
| 3 | Student week journey | ✅ |
| 3b | Week quiz path | ✅ |
| 4–5 | Quiz detail + play | ✅ |
| 6–8 | Teacher grading | ✅ |
| 9–14 | Admin users + quizzes | ✅ |
| 15–17 | Admin weeks CRUD | ✅ |

## Setup

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed && php artisan storage:link
npm run dev && php artisan serve
```

## Seed accounts

| Username | Password | Notes |
|----------|----------|-------|
| student | password | Week 1 unlocked |
| student_allweeks | password | All 4 weeks unlocked |
| student_jan1 | password | Registered 2026-01-01 |
| teacher | password | Grading |
| admin | password | Weeks + quizzes + users |

**Program:** 4 weeks × 4 quizzes auto-assigned via seeders (Choose + Speak). Legacy: Speaking Basics, Grammar MC Drill (unassigned).

## Tests — run full suite

```bash
php artisan test
```

| Area | Test file |
|------|-----------|
| Auth | `tests/Feature/Auth/LoginTest.php` |
| Roles | `tests/Feature/Authorization/RoleAccessTest.php` |
| Admin users | `tests/Feature/Admin/UserManagementTest.php` |
| Admin weeks | `tests/Feature/Admin/WeekManagementTest.php` |
| Submissions | `tests/Feature/Submissions/SubmissionLifecycleTest.php` |
| Auto-grade | `tests/Feature/Grading/AutoGradingTest.php` |
| Teacher grading | `tests/Feature/Teacher/GradingTest.php` |
| Week unlock | `tests/Feature/Student/WeekUnlockTest.php` |
| Journey UI | `tests/Feature/Student/WeekJourneyTest.php` |
| Program seed | `tests/Feature/Student/WeekProgramSeedTest.php` |
| Time limit | `tests/Feature/Student/QuizTimeLimitCompleteTest.php` |
| Catalog | `tests/Feature/Student/QuizCatalogTest.php` |
| Unit | `tests/Unit/**` |

## Flow docs

- [phase-1-epic-1-authentication-sequence.md](flows/phase-1-epic-1-authentication-sequence.md)
- [phase-1-epic-2-authorization-sequence.md](flows/phase-1-epic-2-authorization-sequence.md)
- [phase-2-epic-1-week-gamification-sequence.md](flows/phase-2-epic-1-week-gamification-sequence.md)
- [phase-2-epic-2-admin-week-management-sequence.md](flows/phase-2-epic-2-admin-week-management-sequence.md)
- [phase-1-epic-10-teacher-grading-sequence.md](flows/phase-1-epic-10-teacher-grading-sequence.md)
