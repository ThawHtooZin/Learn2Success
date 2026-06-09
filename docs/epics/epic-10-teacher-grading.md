# Epic 10 — Teacher Grading

**Status:** ✅ Done (enhanced)  
**Depends:** E7, E8, E11  
**PRD:** Module 10 · FR-T1–T9 · NFR-A1, R2, R3, U4 · Screens 6–8

## Goal

Teacher grading queue, by-student view, grade form with audio playback, read-only quiz context, mark validation (per-question + total), transactional save.

## Grade form

### Read-only quiz panel

Teachers **cannot edit quizzes**. Grade screen shows:

- Total marks, mark per question (max per answer)
- Description, question count, time limit, week (if assigned)
- Question type tags (Choose / Speak)

### Per-answer grading

- Question text + type tag
- Recording: audio player + “max X marks” hint
- Multiple choice: choices with student selection and correct answer highlighted
- Mark input capped at `quiz.effectiveMarkPerQuestion()`
- Per-question feedback + overall feedback

### Validation

| Layer | Rule |
|-------|------|
| **UI (Alpine)** | Running total ≤ quiz total; each mark ≤ per-question max; Save disabled when invalid |
| **Server (`GradingService`)** | Same rules; field errors on `answers.{id}.mark` or `total_mark`; transactional rollback |

## GradingService rules

1. Validate all marks before persisting
2. Each answer mark ≤ `effectiveMarkPerQuestion()`
3. Sum ≤ `quiz.total_marks`
4. DB transaction; update answers + submission
5. Rollback on failure (NFR-R3)

## Files

| File | Purpose |
|------|---------|
| `resources/views/teacher/submissions/show.blade.php` | Quiz info + validation UI |
| `resources/js/teacher-grading.js` | Live total / per-question validation |
| `app/Services/Grading/GradingService.php` | Server validation + save |
| `tests/Feature/Teacher/GradingTest.php` | Cap + display tests |

## Tests

- Grade form shows quiz total and per-question max
- Per-question over-max → validation error, status unchanged
- Total over-max → validation error, status unchanged
- Valid save → graded + total_mark

## Flow doc

`docs/flows/phase-1-epic-10-teacher-grading-sequence.md`
