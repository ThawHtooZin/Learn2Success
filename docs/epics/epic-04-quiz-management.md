# Epic 4 — Quiz Management (Admin)

**Status:** ✅ Done
**Depends:** E3, E5  
**PRD:** Module 4 · FR-Q1–Q6 · Screens 12–14

## Goal

Admin quiz CRUD with dynamic questions (1–100); full question replace on edit.

## Create

| File | Purpose |
|------|---------|
| `database/migrations/*_create_quizzes_table.php` | See database-schema.md |
| `database/migrations/*_create_questions_table.php` | |
| `app/Models/Quiz.php` | relations, scopes |
| `app/Models/Question.php` | casts meta JSON, type enum |
| `app/Services/Quizzes/QuizPersistenceService.php` | create, update, delete, replace questions |
| `app/Http/Controllers/Admin/QuizController.php` | resource except show |
| `app/Http/Requests/Admin/StoreQuizRequest.php` | quiz + questions array |
| `app/Http/Requests/Admin/UpdateQuizRequest.php` | same |
| `database/factories/QuizFactory.php` | |
| `database/factories/QuestionFactory.php` | |
| `database/seeders/QuizSeeder.php` | sample quizzes |
| `database/seeders/QuestionSeeder.php` | sample questions |
| `resources/views/admin/quizzes/index.blade.php` | list + question count, active flag |
| `resources/views/admin/quizzes/create.blade.php` | |
| `resources/views/admin/quizzes/edit.blade.php` | |
| `resources/views/admin/quizzes/_form.blade.php` | dynamic question repeater |
| `resources/js/quiz-form.js` | Alpine: add/remove/reorder questions |
| `tests/Feature/Admin/QuizManagementTest.php` | |

## Modify

| File | Change |
|------|--------|
| `routes/web.php` | `Route::resource('manage/quizzes', QuizController::class)->except(['show'])` |
| `database/seeders/DatabaseSeeder.php` | call QuizSeeder, QuestionSeeder |

## Routes

| Method | Path | Name |
|--------|------|------|
| GET | `/manage/quizzes` | `manage.quizzes.index` |
| GET | `/manage/quizzes/create` | `manage.quizzes.create` |
| POST | `/manage/quizzes` | `manage.quizzes.store` |
| GET | `/manage/quizzes/{quiz}/edit` | `manage.quizzes.edit` |
| PUT/PATCH | `/manage/quizzes/{quiz}` | `manage.quizzes.update` |
| DELETE | `/manage/quizzes/{quiz}` | `manage.quizzes.destroy` |

## QuizPersistenceService rules

1. On create: save quiz + questions with `sort_order`
2. On update: **delete all existing questions**, recreate from input
3. After save: if `mark_per_question` null or 0 → set `total_marks / question_count`
4. Delegate each question to `QuestionValidationService` (E5)
5. Wrap in DB transaction

## Form fields

**Quiz:** title, description, is_active, total_marks, mark_per_question (optional)  
**Questions (1–100):** question_text, question_type, choices[], correct_option_indexes[]

## Tests

- Create quiz with mixed question types
- Edit replaces all question IDs (old IDs gone)
- mark_per_question auto-calculated
- Reject 0 or 101 questions
- Invalid question meta rejected

## Acceptance criteria

- [ ] Teachers cannot access `/manage/quizzes`
- [ ] List shows question count + active flag
- [ ] Delete quiz cascades questions
- [ ] Staff layout; Quizzes nav active

## Flow doc to create

`docs/flows/phase-1-epic-4-quiz-management-sequence.md`
