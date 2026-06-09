# Epic 9 — Auto-Grading (Structured Questions)

**Status:** ✅ Done
**Depends:** E7, E8  
**PRD:** Module 9 · FR-G1–G4 · NFR-A2

## Goal

Auto-grade all-multiple-choice quizzes on completion; mixed quizzes stay pending.

## Create

| File | Purpose |
|------|---------|
| `app/Services/Grading/AutoGradingService.php` | |
| `tests/Unit/Grading/AutoGradingServiceTest.php` | |
| `tests/Feature/Grading/AutoGradingTest.php` | end-to-end on complete |

## Modify

| File | Change |
|------|--------|
| `app/Services/Submissions/SubmissionService.php` or complete handler | call AutoGradingService |

## AutoGradingService API

```php
shouldAutoGrade(Quiz $quiz): bool    // every question is multiple_choice
grade(Submission $submission): void  // transaction
```

## grade() logic

1. If not all MC → return early (status stays `pending`)
2. Per answer:
   - Compare `selected_options` vs `correct_option_indexes` using **sorted unique** arrays
   - Set `is_auto_correct`, `mark` = `mark_per_question` or `total_marks / count`
3. Sum marks → `submission.total_mark`
4. Set `submission.status = graded`

## Tests

- All-MC quiz: complete → status graded, correct marks
- Mixed quiz (recording + MC): complete → status pending
- Partial MC wrong → mark 0 for that answer
- Sorted index comparison: `[1,0]` equals `[0,1]` for speaking_pattern

## Acceptance criteria

- [ ] Runs inside completion transaction
- [ ] No teacher action needed for all-MC quizzes
- [ ] recording / speaking_pattern in mixed quiz never auto-graded

## Flow doc to create

`docs/flows/phase-1-epic-9-auto-grading-sequence.md`
