# Epic 6 — Student Quiz Catalog & Attempts

**Status:** ✅ Done  
**Depends:** E4, E8, E11  
**PRD:** Module 6 · FR-S1, S3, S13, Q7, Z5 · Screens 4–5

> **Note:** Student **home** (`/student`) was replaced by the Phase 2 week journey (P2-E1). Quiz detail, play guards, and attempt logic from this epic remain in `QuizController` + `QuizCatalogService`.

## Implemented

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Student/QuizController.php` | show, play guards, new try |
| `app/Services/Student/QuizCatalogService.php` | status labels, latest attempt |
| `resources/views/student/quizzes/show.blade.php` | detail + attempts + type tags |
| `tests/Feature/Student/QuizCatalogTest.php` | active weeks on dashboard |

## Acceptance criteria

- [x] Quiz detail with attempt history
- [x] Start / Continue / New try flows
- [x] Play route guards
- [x] Inactive quiz 404
- [x] Week unlock guard (Phase 2)
