# Phase 1 — Technical Design & Epics

**Product source of truth:** `docs/learn2earn-prd.md`  
**Status:** All Phase 1 epics (E1–E12) implemented ✅  
**Phase 2:** See [phase-2-technical-design-and-tasks.md](phase-2-technical-design-and-tasks.md) (P2-E1 ✅)

**Rule:** One Epic = one PRD Module. Phase 1 is complete; new work continues in Phase 2 epics.

**Implementation docs:**
- [Epic specs](epics/README.md)
- [Implementation status](implementation-status.md)
- [Database schema](database-schema.md)
- [Routes reference](routes-reference.md)

---

## 1. Why / What / Result

| | |
|---|---|
| **Why** | Replace vibe-coded logic with enforced domain rules, services, and tests. |
| **What** | **Learn2Success** platform: auth, roles, admin content, student attempts, teacher grading, responsive UI. |
| **Result** | Maintainable Laravel app with PRD parity, Pest coverage on critical paths, and `docs/flows/` sequence docs. |

---

## 2. Stack & Conventions

| Area | Choice |
|------|--------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Blade, Tailwind CSS v4, Alpine.js, Quicksand font |
| DB | SQLite (dev), MySQL (prod) |
| Storage | `public` disk; `storage:link` for audio |
| Tests | PHPUnit/Pest feature + unit tests (**47** total including Phase 2) |
| Security | CSRF, `$fillable`, server validation, Blade escaping |

**Project rules (from `.cursorrules`):**
- One migration file per table.
- One dedicated Seeder per model.
- Thin controllers; domain logic in services.
- Flow docs in `docs/flows/` per epic.

---

## 3. Architecture

```
Request → Middleware (auth, role) → Controller → Service → Model/DB
                                      ↓
                                   Blade + Alpine.js
```

**Middleware:** `auth`, `guest`, `role:student|teacher|admin`  
**Ownership:** Student submission APIs verify `submission.user_id === auth()->id()`.

**Phase 2 additions:** `WeekUnlockService`, `WeekJourneyService` — see Phase 2 doc.

---

## 4. Domain Model

```
Week 1──* Quiz 1──* Question
User 1──* Submission *──1 Quiz
              └──* Answer *──1 Question
```

| Model | Key fields |
|-------|------------|
| **User** | `username`, `password`, `role`, `created_at` (week unlock anchor) |
| **Week** | `title`, `week_number`, `unlock_after_days`, `sort_order` |
| **Quiz** | `title`, `description`, `is_active`, marks, `week_id`, `time_limit_seconds`, `sort_order_in_week` |
| **Question** | `quiz_id`, `question_text`, `question_type`, `meta`, `sort_order` |
| **Submission** | `user_id`, `quiz_id`, `status`, marks, timestamps, `duration_seconds` |
| **Answer** | `submission_id`, `question_id`, `audio_path`, `selected_options`, marks |

---

## 5. Database

Full definitions: **[database-schema.md](database-schema.md)**

| Table | Status |
|-------|--------|
| `users`, `sessions` | ✅ |
| `quizzes`, `questions` | ✅ |
| `submissions`, `answers` | ✅ |
| `weeks` | ✅ Phase 2 |

---

## 6. Services

| Service | Responsibility |
|---------|----------------|
| `QuestionValidationService` | Question type + meta validation |
| `QuizPersistenceService` | Quiz CRUD; full question replace on edit |
| `SubmissionService` | Start try, in-progress guard, complete (+ time_expired) |
| `AudioUploadService` | Audio MIME/size, storage path |
| `AutoGradingService` | All-MC auto-grade |
| `GradingService` | Teacher grading + cap |
| `QuizCatalogService` | Latest attempt, status labels |
| `WeekUnlockService` | Week unlock from `user.created_at` |
| `WeekJourneyService` | Journey map + quiz step states |

---

## 7. Routes

Full contract: **[routes-reference.md](routes-reference.md)** — all routes ✅ implemented.

---

## 8. UI Layouts

| Layout | Roles | Current behavior |
|--------|-------|------------------|
| `layouts/guest` | Guest | Marketing home + login |
| `layouts/staff` | Teacher, Admin | Sidebar + navbar; gold brand |
| `layouts/student` | Student | **Full-width mobile-first**, bottom nav, journey UI |

Design tokens: **DESIGN.md**

---

## 9. Frontend Modules

| Asset | Purpose |
|-------|---------|
| `resources/js/quiz-play.js` | Play UI, recording, timer, complete |
| `resources/js/student-journey.js` | Journey path animations |
| `resources/js/carousel.js` | Home carousel |
| `resources/js/quiz-form.js` | Admin dynamic questions |

---

## 10. Epic Plan — Phase 1 (all ✅)

**Build order:** `E1 → E2 → E11 → E12 → E5 → E3 → E4 → E8 → E6 → E7 → E9 → E10`

| Epic | Spec | Status |
|------|------|--------|
| E1 Authentication | [epic-01](epics/epic-01-authentication.md) | ✅ |
| E2 Authorization | [epic-02](epics/epic-02-authorization.md) | ✅ |
| E11 UI Shell | [epic-11](epics/epic-11-ui-shell.md) | ✅ |
| E12 Branding | [epic-12](epics/epic-12-branding.md) | ✅ |
| E5 Question Engine | [epic-05](epics/epic-05-question-engine.md) | ✅ |
| E3 User Admin | [epic-03](epics/epic-03-user-administration.md) | ✅ |
| E4 Quiz Management | [epic-04](epics/epic-04-quiz-management.md) | ✅ |
| E8 Submission Lifecycle | [epic-08](epics/epic-08-submission-lifecycle.md) | ✅ |
| E6 Student Catalog | [epic-06](epics/epic-06-student-catalog.md) | ✅ (home superseded by P2-E1 journey) |
| E7 Quiz Play | [epic-07](epics/epic-07-quiz-play.md) | ✅ |
| E9 Auto-Grading | [epic-09](epics/epic-09-auto-grading.md) | ✅ |
| E10 Teacher Grading | [epic-10](epics/epic-10-teacher-grading.md) | ✅ |

**Progress:** [implementation-status.md](implementation-status.md)

---

## 11. Related Docs

| Document | Purpose |
|----------|---------|
| `docs/learn2earn-prd.md` | Product requirements |
| `docs/phase-2-technical-design-and-tasks.md` | Phase 2 |
| `docs/flows/*` | Sequence + QA per epic |
