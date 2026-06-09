# Epic 1 — Authentication & Session

**Status:** ✅ Done  
**Depends:** —  
**PRD:** Module 1 · FR-A1–A5

## Goal

Session-based username login with role redirect after sign-in.

## Implemented

| Item | Path |
|------|------|
| User model | `app/Models/User.php` |
| UserRole enum | `app/Enums/UserRole.php` |
| Login controller | `app/Http/Controllers/Auth/LoginController.php` |
| Login request | `app/Http/Requests/LoginRequest.php` |
| Home controller | `app/Http/Controllers/HomeController.php` |
| Users migration | `database/migrations/0001_01_01_000000_create_users_table.php` |
| Sessions migration | `database/migrations/0001_01_01_000003_create_sessions_table.php` |
| User factory/seeder | `database/factories/UserFactory.php`, `database/seeders/UserSeeder.php` |
| Guest layout | `resources/views/layouts/guest.blade.php` |
| Home + login views | `resources/views/home.blade.php`, `resources/views/auth/login.blade.php` |
| Tests | `tests/Feature/Auth/LoginTest.php` |
| Flow doc | `docs/flows/phase-1-epic-1-authentication-sequence.md` |

## Routes

| Method | Path | Name |
|--------|------|------|
| GET | `/` | `home` |
| GET/POST | `/login` | `login` |
| POST | `/logout` | `logout` |

## Acceptance criteria

- [x] Username + password login, remember-me
- [x] Session regenerate on login; invalidate on logout
- [x] Role redirect: student → `/student`, teacher → `/teacher/submissions`, admin → `/manage/quizzes`
- [x] Guest-only login; auth on protected routes
