# Database Schema Reference

**Rule:** One migration file per table. No bundled migrations.  
**App:** Learn2Success · Laravel 13

---

## Entity relationships

```
weeks 1──* quizzes 1──* questions
users 1──* submissions *──1 quizzes
              └──* answers *──1 questions
```

---

## `users` ✅

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| username | string unique | login identifier |
| password | string | hashed |
| role | string | `student`, `teacher`, `admin` |
| remember_token | string nullable | |
| created_at, updated_at | timestamps | `created_at` anchors week unlock |

---

## `sessions` ✅

Laravel session driver table.

---

## `weeks` ✅ Phase 2

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| title | string | e.g. "Week 1 — Foundations" |
| week_number | unsigned integer unique | 1-based |
| unlock_after_days | unsigned integer | `(week_number - 1) × 7` |
| description | text nullable | |
| is_active | boolean default true | |
| sort_order | unsigned integer | display order |
| created_at, updated_at | timestamps | |

**Unlock:** available when `today >= user.created_at.startOfDay() + unlock_after_days`

---

## `quizzes` ✅

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| week_id | FK → weeks nullable | null = legacy unscoped quiz |
| title | string | |
| description | text nullable | |
| is_active | boolean default true | inactive hidden from students |
| total_marks | unsigned integer | |
| mark_per_question | decimal(8,2) nullable | default `total_marks / question_count` |
| time_limit_seconds | unsigned integer nullable | play countdown; null = no limit |
| sort_order_in_week | unsigned integer default 0 | order inside week path |
| created_at, updated_at | timestamps | |

---

## `questions` ✅

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| quiz_id | FK → quizzes | cascade delete |
| question_text | text | |
| question_type | string | `recording`, `multiple_choice` |
| meta | json nullable | `{ choices: [], correct_option_indexes: [] }` |
| sort_order | unsigned integer | 0-based order |
| created_at, updated_at | timestamps | |

**Student tags:** `recording` → Speak · `multiple_choice` → Choose

**Index:** `(quiz_id, sort_order)`

---

## `submissions` ✅

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | FK → users | |
| quiz_id | FK → quizzes | |
| status | string | `pending`, `graded` |
| total_mark | decimal(8,2) nullable | |
| teacher_feedback | text nullable | |
| started_at | timestamp | |
| completed_at | timestamp nullable | null = in progress |
| duration_seconds | unsigned integer nullable | |
| created_at, updated_at | timestamps | |

**Index:** `(user_id, quiz_id, created_at)` · multi-attempt allowed

---

## `answers` ✅

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| submission_id | FK → submissions | cascade delete |
| question_id | FK → questions | |
| audio_path | string nullable | `recordings/{submission_id}/...` |
| selected_options | json nullable | option indexes |
| mark | decimal(8,2) nullable | |
| teacher_feedback | text nullable | |
| is_auto_correct | boolean nullable | MC auto-grade |
| created_at, updated_at | timestamps | |

**Unique:** `(submission_id, question_id)`

---

## Seeders (one file per model)

| Seeder | Purpose |
|--------|---------|
| `UserSeeder` | student, student_allweeks, student_jan1, teacher, admin |
| `WeekSeeder` | 4 program weeks |
| `QuizSeeder` | 16 program quizzes (4 slots/week) + 2 legacy |
| `QuestionSeeder` | Choose + Speak questions per slot |
| `SubmissionSeeder` | optional demo |
| `AnswerSeeder` | optional demo |

**Program quiz slots per week:**

1. Quick Choose — 3 MC  
2. Grammar Choose — 3 MC  
3. Speak Up — 2 recording  
4. Choose & Speak — 2 MC + 1 recording  

`DatabaseSeeder` calls each seeder individually.
