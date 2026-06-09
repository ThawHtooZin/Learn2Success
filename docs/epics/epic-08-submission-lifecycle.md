# Epic 8 — Submission Lifecycle

**Status:** ✅ Done
**Depends:** E4, E5  
**PRD:** Module 8 · FR-S2, S5, S12 · NFR-R1, A3

## Goal

Submission + Answer models, transactional start/complete, one in-progress guard per student/quiz.

## Create

| File | Purpose |
|------|---------|
| `database/migrations/*_create_submissions_table.php` | |
| `database/migrations/*_create_answers_table.php` | |
| `app/Models/Submission.php` | relations, helpers |
| `app/Models/Answer.php` | |
| `app/Enums/SubmissionStatus.php` | `pending`, `graded` |
| `app/Services/Submissions/SubmissionService.php` | start, complete, guards |
| `database/factories/SubmissionFactory.php` | |
| `database/factories/AnswerFactory.php` | |
| `database/seeders/SubmissionSeeder.php` | optional demo |
| `database/seeders/AnswerSeeder.php` | optional demo |
| `tests/Feature/Submissions/SubmissionLifecycleTest.php` | |
| `tests/Unit/Submissions/SubmissionModelTest.php` | |

## Modify

| File | Change |
|------|--------|
| `app/Models/User.php` | `hasMany submissions` |
| `app/Models/Quiz.php` | `hasMany submissions` |
| `app/Services/Authorization/SubmissionOwnershipGuard.php` | accept `Submission` model |

## Submission model helpers

```php
isInProgress(): bool          // completed_at === null
isAwaitingGrading(): bool     // status pending && completed_at !== null
isGraded(): bool              // status === graded
```

## SubmissionService API

```php
startTry(User $student, Quiz $quiz): Submission   // transaction + answer rows
ensureCanStartNewTry(User $student, Quiz $quiz): void
complete(Submission $submission, int $durationSeconds): void
getInProgress(User $student, Quiz $quiz): ?Submission
```

## startTry flow

1. `ensureCanStartNewTry` — abort if in-progress exists
2. Begin transaction
3. Create submission: `status=pending`, `started_at=now()`, `completed_at=null`
4. Create empty `Answer` row per quiz question (ordered)
5. Commit

## Domain rules

- **One** in-progress (`completed_at` null) per student per quiz
- Multi-attempt: index `(user_id, quiz_id, created_at)`, no unique pair constraint
- Answer unique `(submission_id, question_id)`

## Tests

- startTry creates submission + N answers in transaction
- Second in-progress blocked
- isAwaitingGrading() correct for each state
- Rollback on failure leaves no partial rows

## Acceptance criteria

- [ ] Migrations match database-schema.md
- [ ] One seeder file per model
- [ ] No student UI in this epic — service + models only

## Flow doc to create

`docs/flows/phase-1-epic-8-submission-lifecycle-sequence.md`
