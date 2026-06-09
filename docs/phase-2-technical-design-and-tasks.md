# Phase 2 — Technical Design & Epics

**Product source of truth:** `docs/learn2earn-prd.md` (Phase 2 extensions below)  
**Status:** Phase 2, Epic 2 complete ✅  
**Rule:** One Epic at a time; wait for approval before the next Phase 2 epic.

**Depends on:** Phase 1 complete (E1–E12 ✅)

---

## 1. Why / What / Result

| | |
|---|---|
| **Why** | Flat quiz lists do not motivate young learners; content should unlock over time like a learning game. |
| **What** | Day-based **Week Management**: quizzes grouped into weeks, unlocked on a schedule from the student’s registration date. Gamified **Coddy-style** step-by-step journey UI with mobile page transitions. Per-quiz **time limits**. No energy system in this epic. |
| **Result** | Students see a vertical week path; each unlocked week exposes quiz steps they can take and retake anytime. Admins assign quizzes to weeks from **Weeks → Edit week** only; quiz edit sets time limit only. |

---

## 2. Domain Rules

### Week unlock (day-based)

- Each student’s **anchor date** = `users.created_at` (start of day).
- Week `N` has `unlock_after_days = (N − 1) × 7`.
  - Week 1 → day 0 (registration day)
  - Week 2 → day 7 (e.g. register Jan 1 → unlock Jan 8)
  - Week 3 → day 14
  - Week 4 → day 21
- Unlocked weeks stay unlocked forever.
- Quizzes inside an unlocked week are **always playable** (take + retake, multi-attempt unchanged from Phase 1).

### Quiz time limit

- `quizzes.time_limit_seconds` — nullable; when set, the play UI shows a countdown and auto-completes the attempt when time runs out.
- Server validates `duration_seconds` on complete (soft guard; client timer is UX).

### Out of scope (Phase 2 so far)

- Energy / lives / hearts
- Streaks, badges, leaderboards

---

## 3. Architecture

```
Student GET /student
  → WeekJourneyService → journey map (week nodes)

Student GET /student/weeks/{week}
  → WeekUnlockService (403 if locked)
  → WeekJourneyService → quiz step path

Student GET /student/quizzes/{quiz}/play
  → WeekUnlockService via quiz.week
  → existing SubmissionService + time limit in JS
```

**New services**

| Service | Responsibility |
|---------|----------------|
| `WeekUnlockService` | `isUnlocked`, `unlocksAt`, `daysUntilUnlock` |
| `WeekJourneyService` | Build week map + quiz step states for views |

---

## 4. Domain Model (additions)

```
Week 1──* Quiz (optional week_id on existing quizzes)
User.created_at → unlock schedule
```

| Model | Key fields |
|-------|------------|
| **Week** | `title`, `week_number`, `unlock_after_days`, `description`, `is_active`, `sort_order` |
| **Quiz** (extended) | `week_id` FK nullable, `time_limit_seconds` nullable, `sort_order_in_week` |

---

## 5. Database

See **[database-schema.md](database-schema.md)** — `weeks` table + `quizzes` columns.

**Migrations (one file each):**

1. `create_weeks_table`
2. `add_week_fields_to_quizzes_table`

**Seeders (one file each):**

| Seeder | Purpose |
|--------|---------|
| `WeekSeeder` | 4 weeks (1 month program) |
| `QuizSeeder` | 16 program quizzes per week (slots 0–3: Quick Choose, Grammar Choose, Speak Up, Choose & Speak) + 2 legacy unassigned |
| `QuestionSeeder` | Choose (MC) + Speak (recording) per slot — see flow doc |
| `UserSeeder` | `student`, `student_allweeks`, `student_jan1` for unlock QA |

---

## 6. Routes (student)

| Method | Path | Name |
|--------|------|------|
| GET | `/student` | `student.dashboard` — week journey map |
| GET | `/student/weeks/{week}` | `student.weeks.show` — quiz step path |
| *(existing quiz routes unchanged)* | | unlock guard added |

---

## 7. UI — Coddy-style journey (Epic 1)

| Screen | Pattern |
|--------|---------|
| **Journey map** | Vertical path; week nodes (locked / unlocked / completed); no card grid |
| **Week detail** | Numbered quiz steps on a connected trail; tap step → quiz detail |
| **Transitions** | Slide/fade page transitions (`view-transition` + Alpine); node bounce on active |
| **Play** | Existing play UI + prominent countdown when `time_limit_seconds` set |

Palette: `DESIGN.md` gold / sky / green. Full-width mobile shell.

**Question types in program seed:** Choose (MC) + Speak (recording) only. Tags on quiz steps; Record button on Speak questions in play UI.

---

## 8. Epic index (Phase 2)

| Epic | Name | Status |
|------|------|--------|
| **P2-E1** | Week Management & Gamification Journey | ✅ Done |
| **P2-E2** | Admin Week Management | ✅ Done |

Specs: **[epic-13-week-gamification.md](epics/epic-13-week-gamification.md)** · **[epic-14-admin-week-management.md](epics/epic-14-admin-week-management.md)**

---

## 9. Admin routes (P2-E2)

| Method | Path | Name |
|--------|------|------|
| GET | `/admin/weeks` | `admin.weeks.index` |
| GET | `/admin/weeks/create` | `admin.weeks.create` |
| POST | `/admin/weeks` | `admin.weeks.store` |
| GET | `/admin/weeks/{week}/edit` | `admin.weeks.edit` |
| PUT/PATCH | `/admin/weeks/{week}` | `admin.weeks.update` |
| DELETE | `/admin/weeks/{week}` | `admin.weeks.destroy` |

| POST | `/admin/weeks/{week}/quizzes` | `admin.weeks.quizzes.store` |
| PUT | `/admin/weeks/{week}/quizzes/reorder` | `admin.weeks.quizzes.reorder` |
| DELETE | `/admin/weeks/{week}/quizzes/{quiz}` | `admin.weeks.quizzes.destroy` |

---

## 10. Testing

```bash
php artisan migrate:fresh --seed
php artisan test   # 55 tests total
```

**Phase 2 automated tests**

| File | Covers |
|------|--------|
| `tests/Feature/Student/WeekUnlockTest.php` | Unlock rules, 403 |
| `tests/Feature/Student/WeekJourneyTest.php` | Journey + week detail |
| `tests/Feature/Student/WeekProgramSeedTest.php` | Seeder slots, Choose/Speak, Record UI |
| `tests/Feature/Student/QuizTimeLimitCompleteTest.php` | `time_expired` submit |
| `tests/Feature/Admin/WeekManagementTest.php` | Admin week CRUD + auth |

**Manual QA accounts**

| Username | Password | Unlock behavior |
|----------|----------|-----------------|
| `student` | password | Week 1 only (registered recently) |
| `student_allweeks` | password | All 4 weeks unlocked |
| `student_jan1` | password | Registered Jan 1, 2026 — week unlocks follow calendar |

---

## 11. Flow docs

- [docs/flows/phase-2-epic-1-week-gamification-sequence.md](flows/phase-2-epic-1-week-gamification-sequence.md)
- [docs/flows/phase-2-epic-2-admin-week-management-sequence.md](flows/phase-2-epic-2-admin-week-management-sequence.md)
