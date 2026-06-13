# Product Requirements Document (PRD)

## Learn2Success — English Speaking Practice & Assessment Platform

> **Note:** Product branded **Learn2Success** (repo folder may still be `Learn2EarnV2`). This PRD is the product source of truth for Phase 1 + Phase 2.

### 1. Project Description

#### Problems

- English speaking practice is often done outside structured systems (chat apps, ad-hoc recordings, paper rubrics), making attempts hard to track and scores inconsistent.
- Teachers lack a single queue to review spoken answers with per-question marks and feedback tied to each attempt.
- Students cannot easily see attempt history, resume in-progress work, or understand graded vs pending vs in-progress states.
- Generic quiz tools do not support browser-based audio capture, timed recording, and teacher listening workflows in one mobile-friendly product.
- Vibe-coded implementations risk unclear domain rules (one active attempt per quiz, grading caps, question-type behavior), leading to bugs and difficult rebuilds.

#### Aims & Objectives

- Provide a **speaking-first assessment platform** where students answer quiz questions (primarily by voice) and teachers grade with marks and feedback.
- Support **role-based workflows** for **Student**, **Teacher**, and **Admin** with strict route and permission separation.
- Enable **multi-attempt** practice per quiz with full attempt history, duration tracking, and status lifecycle.
- Support **multiple question types**: recording (spoken), multiple choice, and speaking pattern (multi-select options), with auto-grading where applicable.
- Give admins **quiz authoring** and **user provisioning**; teachers focus on **grading**, not content management.
- Deliver a **mobile-first responsive web app** (phone browser primary for students) using Laravel, Tailwind CSS, and Alpine.js.
- Establish this PRD plus use-case and UML artifacts as the **source of truth** for a proper rebuild (not vibe-coded).

---

### 2. Scope of the Project

#### Project Deliverables

##### Module 1 — Authentication & Session

- Marketing home page with sign-in entry.
- Username + password login with optional remember-me.
- Session-based authentication; logout invalidates session.
- Post-login redirect by role: student → week journey, teacher → **teacher dashboard**, admin → **admin dashboard** (Module 15).
- Guest-only login routes; authenticated routes protected by `auth` middleware.

##### Module 2 — Authorization & Role Access

- Three roles: `student`, `teacher`, `admin`.
- Role middleware on route groups (`role:student`, `role:teacher`, `role:admin`).
- Students cannot access teacher or admin paths; teachers cannot access admin paths.
- Ownership checks on student submission APIs (user must own submission).

##### Module 3 — User Administration (Admin)

- List users with pagination.
- Create user: username, password (min 8, confirmed), role.
- Edit user: username, role, optional password change (blank keeps current).
- Delete user with guard against self-deletion.
- No dedicated user “show” page.

##### Module 4 — Quiz Management (Admin)

- List quizzes with question count, active flag, edit/delete actions.
- Create quiz: title, description, active flag, `total_marks`, optional `mark_per_question`, optional `time_limit_seconds`.
- Dynamic question list on create/edit (1–100 questions).
- Edit quiz: **full replace** of all questions on save (delete existing questions, recreate).
- Delete quiz.
- Default `mark_per_question` computed as `total_marks / question_count` when not set or zero.
- **Week assignment** (`week_id`, order in week) is **not** on quiz edit — managed only from **Weeks → Edit week** (Module 14).
- Teachers **do not** create or edit quizzes in the current product.

##### Module 5 — Question Engine

- Questions belong to one quiz; ordered by `sort_order`.
- Question types:
  - **`recording`** — student records audio; teacher grades manually.
  - **`multiple_choice`** — single correct option; auto-graded on completion when quiz is all MC.
- Structured `meta` JSON: `choices[]`, `correct_option_indexes[]`.
- Validation on admin save: MC requires exactly one correct index; choice questions need ≥2 options; correct indexes must be in range.

##### Module 6 — Student Quiz Catalog & Attempts

> **Phase 2 update:** Student home (`/student`) is a **week journey map** (see Module 13). Quiz detail, play, attempts, and guards below still apply to each quiz inside a week.

- **Journey home:** vertical week path; locked/unlocked by registration date (Module 13).
- **Week detail:** numbered quiz steps inside an unlocked week.
- Active quizzes only (`is_active = true`); inactive → 404 on student routes.
- Per-quiz: title, question count, total marks, **question-type tags** (Choose / Speak), optional time limit.
- Status chip from latest attempt; score if graded; **Open**, **Continue** (in-progress).
- Quiz detail: description, meta, try count, attempt list (newest first).
- Attempt states (derived):
  - **In progress** — `completed_at` is null.
  - **Pending** — completed, `status = pending` (awaiting teacher grade).
  - **Graded** — `status = graded`.
- **Start** (first try), **Continue** (resume draft), **New try** (after prior completion; blocked if another in-progress exists).
- Play URL guards: cannot start play directly after graded/awaiting without **New try** from detail page.
- Week-locked quizzes → **403** (student cannot access quizzes in locked weeks).

##### Module 7 — Student Quiz Play & Response Capture

- Sequential question UI with progress indicators.
- Per question type behavior:
  - **Recording:** optional **Listen** (Web Speech API `en-US`); **Record** with 3–2–1 countdown; max **30 seconds** per question; stop, playback, re-record; upload on continue.
  - **Multiple choice:** select one option; save selection via API.
- Resume: previously saved audio URL or selections loaded.
- Upload audio: POST multipart, max **50 MB** per file; allowed MIME types (webm, mp3, m4a, wav, etc.); stored on `public` disk under `recordings/{submission_id}/`.
- Replace prior audio file on re-upload for same answer.
- Complete submission: validates every question answered (unless `time_expired` on quiz timer); sets `completed_at`, `duration_seconds`; auto-grades when all MC.
- **Quiz time limit:** optional `time_limit_seconds` on quiz; countdown in play UI; auto-submit when time runs out.

##### Module 8 — Submission Lifecycle

- Each try = one `Submission` row per student per quiz attempt.
- Fields: `user_id`, `quiz_id`, `status` (`pending` | `graded`), `total_mark`, `teacher_feedback`, `started_at`, `completed_at`, `duration_seconds`.
- **One in-progress submission** per student per quiz at a time (`completed_at` null).
- On new submission: transactional creation of `Answer` row per question.
- `isAwaitingGrading()` = pending + `completed_at` not null.
- Multi-attempt: unique constraint on `(user_id, quiz_id)` removed; index on `(user_id, quiz_id, created_at)`.

##### Module 9 — Auto-Grading (Structured Questions)

- On completion, if every question is `multiple_choice`, submission status set to `graded` immediately.
- Per answer: compare `selected_options` to `correct_option_indexes`; set `is_auto_correct`, `mark` = `mark_per_question` or `total_marks / question_count`.
- Sum answer marks into `submission.total_mark`.
- Recording questions in mixed quizzes require teacher grading.

##### Module 10 — Teacher Grading

- **Teachers only** enter and save marks; admins have read-only access (Module 16).

- Submissions list with filters: **Ready to grade** (pending + completed), **In progress**, **Graded**, **All**; paginated (20).
- By-student view: students with at least one submission; grouped attempts per student.
- Grade screen: **read-only quiz details** (total marks, mark per question, description, question count, time limit, week, question types) — teachers do **not** edit quiz content.
- Student attempt meta: username, timestamps, duration.
- Per question: text, type tag (Speak / Choose), audio player for recording answers, MC choice context (student selection vs correct), mark input with **per-question max**, per-question feedback.
- **Live UI validation:** running total vs quiz maximum; per-question cap; Save disabled when invalid.
- Save grading: transactional update; each answer mark ≤ `mark_per_question` (or `total_marks / question_count`); sum ≤ `total_marks`; sets `status = graded`, `total_mark` = sum of answer marks.

##### Module 11 — UI Shell & Responsive Layouts

- **Staff layout** (`layouts/staff`): AdminLTE-style sidebar + top navbar for teacher and admin; gold/sky brand accents.
- **Student layout** (`layouts/student`): **full-width mobile-first** shell; fixed bottom nav (Home, Log out); sticky header with logo + username; page slide transitions.
- **Guest layout** (`layouts/guest`): marketing home + login; no dashboard nav.
- Flash success/error banners.
- Design system: `DESIGN.md` (Quicksand, gold `#785900`, sky `#006399`, green `#006e1c`).
- Student journey UI: Coddy-style vertical week path and quiz steps (Phase 2).

##### Module 12 — Branding & Marketing

- Product name: **Learn2Success**.
- Public home: logo, tagline (*Together we learn, together we succeed.*), image carousel, **Sign in** CTA, feature chips (Listen / Speak / Succeed).
- Assets: `public/images/branding/`, `public/images/carousel/`, `config/branding.php`.
- `APP_NAME=Learn2Success` in `.env.example`.

##### Module 13 — Week Management & Gamification (Phase 2) ✅

- **Weeks** group quizzes; day-based unlock from `users.created_at`.
- Week 1 → day 0; Week 2 → day 7; Week 3 → day 14; Week 4 → day 21 (configurable via `unlock_after_days`).
- Unlocked weeks stay open; quizzes inside can be taken and **retaken** anytime (multi-attempt unchanged).
- Optional **per-quiz time limit** (`time_limit_seconds`).
- **Program seed:** 4 weeks × 4 quizzes — Quick Choose, Grammar Choose, Speak Up (2 recording), Choose & Speak (MC + recording).
- Student-facing question tags: **Choose** (multiple choice), **Speak** (recording with Record button).
- **Not in scope:** energy/lives, streaks/badges.

##### Module 14 — Admin Week Management (Phase 2) ✅

- Admin **Weeks** CRUD at `/admin/weeks` (sidebar link).
- **Week edit** uses a two-column layout: details left, quiz panel right (full width on desktop).
- Add quizzes from week edit (dropdown + **Add to week**); drag-and-drop reorder (desktop) or ↑↓ (mobile).
- Remove quiz from week without deleting the quiz.
- Fields: title, week number, unlock after days, description, active, sort order.
- **Week ↔ quiz linking** lives here only (add, reorder, remove). Quiz edit keeps **time limit** only.

##### Module 15 — Staff Dashboards (Phase 2) ✅

**Single epic (P2-E3):** role-specific home dashboards for admin and teacher — overview first, lists second.

**Admin dashboard** (`GET /admin`, `admin.dashboard`):

- Landing page after admin login (replaces default to quiz list).
- **KPI cards:** student count, teacher count, active quizzes, pending grades (completed submissions awaiting teacher).
- **Charts:** submissions completed per day (14-day line/area); grading status split (pending / in progress / graded); attempts by week (bar).
- **Quick actions:** create user, create quiz, create week, open grading queue.
- **Recent activity:** latest submissions with student, quiz, status, link to **view submission** (admin read-only route).

**Teacher dashboard** (`GET /teacher`, `teacher.dashboard`):

- Landing page after teacher login (replaces default to submissions list).
- **KPI cards:** ready to grade, graded in last 7 days, in progress, active students (30-day window).
- **Charts:** pending grading workload trend (14 days); status donut.
- **Priority queue:** oldest ready-to-grade submissions (top 5) with direct **Grade** CTA.
- **Quick actions:** full queue, by-student view.

**UX:** Mobile-responsive card grid; brand palette from `DESIGN.md`; Chart.js for graphs; empty states when no data.

**Not in scope:** CSV export, custom date ranges, per-teacher queues, student dashboard.

##### Module 16 — Admin Submission Review (Phase 2) ✅

**Single epic (P2-E4):** admins can **view** submissions and grades; **only teachers** save marks.

- Admin **Submissions** at `/admin/submissions` (sidebar link).
- Same filters and data table as teacher queue: ready to grade, in progress, graded, all.
- **By-student** grouped view at `/admin/submissions/by-student`.
- **Detail view (read-only):** quiz metadata, student answers, audio playback, marks and feedback as text.
- **View only** banner; no grade form or Save button for admin.
- Admin dashboard recent activity links to admin submission detail (not teacher grade route).
- Teacher grading routes (`PUT /teacher/submissions/{submission}`) remain teacher-only.

##### Module 17 — Staff Data Tables (Phase 2) ✅

**Single epic (P2-E5):** reusable search, sort, filter, pagination for staff listing pages.

- `TableQuery` helper + `<x-data-table>` Blade components.
- Applied to: admin Users, Quizzes, Weeks; teacher and admin Submissions index.
- Query-string state for search, sort, filters, and per-page.
- See `docs/data-table-component.md`.

---

#### Screen Catalog (Implemented)

| # | Screen | Route name | Path | Role |
|---|--------|------------|------|------|
| 1 | Marketing home | `home` | `/` | Guest |
| 2 | Sign in | `login` | `/login` | Guest |
| 3 | Student week journey | `student.dashboard` | `/student` | Student |
| 3b | Week quiz path | `student.weeks.show` | `/student/weeks/{week}` | Student |
| 4 | Student quiz detail | `student.quizzes.show` | `/student/quizzes/{quiz}` | Student |
| 5 | Student quiz play | `student.quizzes.play` | `/student/quizzes/{quiz}/play` | Student |
| 6 | Teacher submissions | `teacher.submissions.index` | `/teacher/submissions` | Teacher |
| 7 | Teacher by student | `teacher.submissions.by-student` | `/teacher/submissions/by-student` | Teacher |
| 8 | Teacher grade | `teacher.submissions.show` | `/teacher/submissions/{submission}` | Teacher |
| 9 | Admin users list | `admin.users.index` | `/admin/users` | Admin |
| 10 | Admin create user | `admin.users.create` | `/admin/users/create` | Admin |
| 11 | Admin edit user | `admin.users.edit` | `/admin/users/{user}/edit` | Admin |
| 12 | Admin quizzes list | `manage.quizzes.index` | `/manage/quizzes` | Admin |
| 13 | Admin create quiz | `manage.quizzes.create` | `/manage/quizzes/create` | Admin |
| 14 | Admin edit quiz | `manage.quizzes.edit` | `/manage/quizzes/{quiz}/edit` | Admin |
| 15 | Admin weeks list | `admin.weeks.index` | `/admin/weeks` | Admin |
| 16 | Admin create week | `admin.weeks.create` | `/admin/weeks/create` | Admin |
| 17 | Admin edit week | `admin.weeks.edit` | `/admin/weeks/{week}/edit` | Admin |
| 18 | Admin dashboard | `admin.dashboard` | `/admin` | Admin |
| 19 | Teacher dashboard | `teacher.dashboard` | `/teacher` | Teacher |
| 20 | Admin submissions | `admin.submissions.index` | `/admin/submissions` | Admin |
| 21 | Admin by student | `admin.submissions.by-student` | `/admin/submissions/by-student` | Admin |
| 22 | Admin view submission | `admin.submissions.show` | `/admin/submissions/{submission}` | Admin |

---

#### Domain Model (Core Entities)

| Entity | Purpose | Key relationships |
|--------|---------|-------------------|
| **User** | Account with `username`, `password`, `role`; `created_at` anchors week unlock | has many `Submission` |
| **Week** | Program unit; `week_number`, `unlock_after_days`, `sort_order` | has many `Quiz` |
| **Quiz** | Assessment unit: title, description, `is_active`, marks, optional `week_id`, `time_limit_seconds`, `sort_order_in_week` | belongs to `Week` (optional); has many `Question`, `Submission` |
| **Question** | Ordered item: `question_text`, `question_type`, `meta`, `sort_order` | belongs to `Quiz`; has many `Answer` |
| **Submission** | One student try on one quiz | belongs to `User`, `Quiz`; has many `Answer` |
| **Answer** | Response for one question in one submission | belongs to `Submission`, `Question`; stores `audio_path`, `selected_options`, `mark`, `teacher_feedback`, `is_auto_correct` |

---

#### Project Exclusions

- Native iOS/Android apps (MVP is responsive web only).
- Offline recording or offline sync.
- Real-time collaborative grading or live proctoring.
- AI speech scoring / pronunciation analysis (MVP).
- Student self-registration, email verification, password reset flows (not in current routes).
- Teacher quiz authoring (admin only in current product).
- Public API / third-party LMS integrations.
- Multi-tenant / multi-school organization hierarchy.
- Audit log UI (not implemented; may be added in rebuild).
- CSV export / downloadable reports (dashboards in Module 15 — no export in P2-E3).
- Internationalization beyond Laravel `__()` strings (English first).

#### Project Constraints

- **Stack:** Laravel 13, PHP 8.3+, Tailwind CSS v4, Alpine.js, Quicksand font, MySQL or SQLite (dev).
- **Storage:** `FILESYSTEM_DISK=public`; `php artisan storage:link` required for audio playback.
- **Browser APIs:** Recording depends on `MediaRecorder`; TTS on `speechSynthesis` (browser-dependent).
- **Audio limit:** 30 s recording UI cap; 50 MB upload server validation.
- **Grading integrity:** Total marks must not exceed quiz `total_marks`; enforced server-side in transaction.
- **Question edit semantics:** Saving a quiz **replaces** all questions — destructive for in-flight attempts referencing old question IDs (rebuild should document or mitigate).
- Rebuild should use **tested domain services** and Pest/feature tests for critical paths.

#### Assumptions

- One deployment serves one training program or school cohort (single-tenant).
- Staff provision accounts; students do not self-register in MVP.
- Students primarily use mobile browsers with microphone permission.
- Teachers grade asynchronously (not live during attempt).
- English UI and `en-US` TTS for listen feature.
- All multiple-choice-only quizzes can be auto-graded without teacher action.
- Mixed-type quizzes always need teacher grading for recording (and non-auto parts).

---

### 3. Requirements Specification

#### Functional Requirements

##### Authentication & Session

- FR-A1: The system shall provide a public marketing home page with navigation to sign in.
- FR-A2: The system shall authenticate users with username and password.
- FR-A3: The system shall support optional “remember me” for persistent session.
- FR-A4: The system shall regenerate session on successful login and invalidate on logout.
- FR-A5: The system shall redirect authenticated users to their role-appropriate home route after login (student → journey; teacher → teacher dashboard; admin → admin dashboard).

##### Staff Dashboards (Phase 2 — Module 15)

- FR-D1: The system shall provide an admin dashboard at `/admin` showing program KPIs derived from users, quizzes, weeks, and submissions.
- FR-D2: The system shall provide a teacher dashboard at `/teacher` emphasizing ready-to-grade workload and priority submissions.
- FR-D3: Dashboards shall include at least two chart visualizations per role using server-computed datasets.
- FR-D4: Dashboards shall expose quick-action links to existing CRUD and grading routes without duplicating business logic.
- FR-D5: Dashboards shall be responsive and usable on mobile staff browsers.
- FR-D6: Staff sidebar shall list **Dashboard** as the first navigation item for admin and teacher layouts.

##### Authorization

- FR-Z1: The system shall restrict student routes to users with role `student`.
- FR-Z2: The system shall restrict teacher routes to users with role `teacher`.
- FR-Z3: The system shall restrict admin user and quiz management routes to users with role `admin`.
- FR-Z4: The system shall return 403 when a student accesses another user’s submission APIs.
- FR-Z5: The system shall return 404 when a student accesses an inactive quiz.

##### User Administration

- FR-U1: The system shall allow admins to list, create, edit, and delete users.
- FR-U2: The system shall require username uniqueness, password minimum length 8 with confirmation on create.
- FR-U3: The system shall allow optional password update on edit when a new password is provided.
- FR-U4: The system shall assign each user exactly one role: `student`, `teacher`, or `admin`.
- FR-U5: The system shall prevent admins from deleting their own account.

##### Quiz Management

- FR-Q1: The system shall allow admins to create quizzes with title, description, active flag, total marks, and optional mark per question.
- FR-Q2: The system shall allow admins to add 1–100 questions per quiz with text and type.
- FR-Q3: The system shall validate question types and choice/correct-index rules before save.
- FR-Q4: The system shall replace all questions when a quiz is updated (delete and recreate question rows).
- FR-Q5: The system shall default `mark_per_question` to `total_marks / question_count` when unset or zero after save.
- FR-Q6: The system shall allow admins to delete quizzes.
- FR-Q7: The system shall show active weeks on the student journey home; inactive weeks hidden. Legacy quizzes without `week_id` remain accessible if linked directly.

##### Week Management (Phase 2)

- FR-W1: The system shall group program quizzes into weeks with `unlock_after_days` from student registration date.
- FR-W2: The system shall block access to locked weeks and their quizzes (403).
- FR-W3: The system shall allow take and retake on unlocked week quizzes (multi-attempt preserved).
- FR-W4: The system shall support optional per-quiz `time_limit_seconds` with client countdown and time-expired submit.
- FR-W5: The system shall display question-type tags (Choose / Speak) on quiz steps and detail.
- FR-W6: Admin shall create, edit, list, and delete weeks; assign quizzes to weeks with sort order and time limit from admin UI.

##### Question Types & Content

- FR-QT1: The system shall support question type `recording` requiring an audio upload before completion.
- FR-QT2: The system shall support question type `multiple_choice` with exactly one correct option index.
- FR-QT3: The system shall store choices and correct indexes in question `meta` JSON.
- FR-QT4: The system shall order questions by `sort_order` for play and grading views.

##### Student Attempts & Play

- FR-S1: The system shall allow students to view quiz detail and all their attempts for that quiz.
- FR-S2: The system shall allow only one in-progress submission per student per quiz at a time.
- FR-S3: The system shall block starting a new try while an in-progress submission exists.
- FR-S4: The system shall allow resuming an in-progress submission with saved answers.
- FR-S5: The system shall create a new submission and answer rows transactionally when starting a try.
- FR-S6: The system shall provide quiz play with per-question progress and type-appropriate UI.
- FR-S7: The system shall enforce a 30-second maximum recording duration per question in the client UI.
- FR-S8: The system shall support optional text-to-speech playback of question text via browser speech synthesis.
- FR-S9: The system shall upload and store audio per answer; replacing prior file on re-upload.
- FR-S10: The system shall save selected option indexes for non-recording questions.
- FR-S11: The system shall require all questions to be answered before marking submission complete (except when `time_expired` is true).
- FR-S12: The system shall record `completed_at` and `duration_seconds` on completion.
- FR-S13: The system shall redirect students away from play when they must use **New try** from quiz detail.

##### Auto-Grading

- FR-G1: The system shall auto-grade submissions when every question in the quiz is `multiple_choice`.
- FR-G2: The system shall set per-answer `mark` to mark-per-question when selected options match correct indexes exactly.
- FR-G3: The system shall set submission `status` to `graded` and compute `total_mark` on auto-grade completion.
- FR-G4: The system shall leave submissions as `pending` after completion when manual grading is required.

##### Teacher Grading

- FR-T1: The system shall list submissions filterable by ready-to-grade, in-progress, graded, and all.
- FR-T2: The system shall paginate the teacher submission index.
- FR-T3: The system shall provide a by-student grouped view of attempts.
- FR-T4: The system shall display each answer with audio playback for recording questions.
- FR-T5: The system shall allow teachers to enter per-question marks and feedback plus overall feedback.
- FR-T6: The system shall reject grading when any answer mark exceeds the quiz per-question maximum (`mark_per_question` or computed default).
- FR-T7: The system shall reject grading when sum of marks exceeds quiz `total_marks`.
- FR-T8: The system shall display read-only quiz metadata on the grade screen (total marks, per-question max, description, types) without quiz edit access.
- FR-T9: The system shall set submission status to `graded` and persist `total_mark` on successful save.

##### Admin Submission Review

- FR-A1: The system shall provide admin read-only submission list, by-student, and detail routes separate from teacher grading routes.
- FR-A2: The system shall display submission answers, marks, and feedback to admins without editable grade inputs.
- FR-A3: The system shall restrict grade persistence (`PUT` grading) to the teacher role only.
- FR-A4: The system shall link admin dashboard recent activity to admin submission detail routes.

##### Staff Data Tables

- FR-DT1: Staff listing pages shall support server-side search with query-string persistence.
- FR-DT2: Staff listing pages shall support sortable columns and status filters where applicable.
- FR-DT3: Staff listing pages shall paginate results with configurable page size.

##### Administration & Security

- FR-SEC1: The system shall require authentication for all student, teacher, and admin business routes.
- FR-SEC2: The system shall hash passwords securely.
- FR-SEC3: The system shall validate all inputs server-side.
- FR-SEC4: The system shall protect forms with CSRF tokens.
- FR-SEC5: The system shall validate audio MIME type and size on upload.
- FR-SEC6: The system shall ensure submission–question pairing belongs to the same quiz on API actions.

#### Non-Functional Requirements

##### Performance

- NFR-P1: Student dashboard and quiz detail pages should render in under 2 seconds on typical mobile network conditions.
- NFR-P2: Audio upload API should return success or validation error within 5 seconds for files under 5 MB on normal server load.
- NFR-P3: Teacher submission list should use pagination to avoid loading unbounded rows.

##### Accuracy & Domain Integrity

- NFR-A1: Grading total must never exceed quiz `total_marks` (server-enforced).
- NFR-A2: Auto-grade comparison must use sorted unique option index arrays for equality.
- NFR-A3: Only one in-progress submission per student per quiz must be enforceable at creation time.
- NFR-A4: Inactive quizzes must not be visible or playable by students.

##### Reliability

- NFR-R1: Submission creation and answer row creation must run inside a database transaction.
- NFR-R2: Grading updates must run inside a database transaction.
- NFR-R3: Failed grading validation must not persist partial mark updates.

##### Security

- NFR-S1: Passwords must be stored hashed (Laravel default).
- NFR-S2: Role checks must apply on every protected route group.
- NFR-S3: Students must only upload audio or selections to their own submissions.
- NFR-S4: Blade output must be escaped to prevent XSS.
- NFR-S5: Mass assignment must use explicit `$fillable` on models.

##### Usability

- NFR-U1: Student UI is **full-width mobile-first** with bottom navigation; staff use responsive sidebar layout.
- NFR-U2: Touch targets for record and navigation should be at least 44px where practical.
- NFR-U3: One primary action per screen (Sign in, Continue, Record, Save grading).
- NFR-U4: Teacher grade form shall show live running total and block save when marks exceed quiz or per-question limits.
- NFR-U4: Error messages for mic denied, upload failure, and incomplete answers must be visible to the user.

##### Maintainability

- NFR-M1: Domain logic for grading, auto-score, and audio storage should live in dedicated services where rebuilt.
- NFR-M2: Controllers should remain thin; validation rules centralized and test-covered.
- NFR-M3: PRD, use cases, and UML class diagram must stay aligned with migrations and models.

##### Testability

- NFR-T1: Feature tests cover submission lifecycle, in-progress guard, completion validation (**47 tests** as of Phase 2 E1).
- NFR-T2: Feature tests must cover grading cap and auto-grade for all-MC quizzes.
- NFR-T3: Feature tests must cover role middleware and student ownership on APIs.
- NFR-T4: Unit tests must cover question meta parsing and correct-index validation rules.

##### Auditability (Rebuild Target)

- NFR-AU1: Future versions should log who graded which submission and when (not in current MVP).
- NFR-AU2: Audio files should remain traceable to `submission_id` and `question_id` via storage path convention.

---

### 4. API & Route Contract (Current Implementation)

| Method | Route | Name | Purpose |
|--------|-------|------|---------|
| GET | `/` | `home` | Marketing home |
| GET/POST | `/login` | `login` | Sign in |
| POST | `/logout` | `logout` | Sign out |
| GET | `/student` | `student.dashboard` | Week journey map |
| GET | `/student/weeks/{week}` | `student.weeks.show` | Quiz steps inside week |
| GET | `/student/quizzes/{quiz}` | `student.quizzes.show` | Quiz detail & tries |
| POST | `/student/quizzes/{quiz}/new-try` | `student.quizzes.new-try` | Start new attempt |
| GET | `/student/quizzes/{quiz}/play` | `student.quizzes.play` | Play UI (`?start=1` or `?new=1`) |
| POST | `/student/submissions/{submission}/questions/{question}/audio` | `student.submissions.audio` | Upload recording |
| POST | `/student/submissions/{submission}/questions/{question}/selection` | `student.submissions.selection` | Save selected options |
| POST | `/student/submissions/{submission}/complete` | `student.submissions.complete` | Finish try |
| GET | `/admin` | `admin.dashboard` | Admin overview (P2-E3) |
| GET | `/admin/submissions` | `admin.submissions.index` | Admin submission list (P2-E4) |
| GET | `/admin/submissions/by-student` | `admin.submissions.by-student` | Admin by-student view |
| GET | `/admin/submissions/{submission}` | `admin.submissions.show` | Admin read-only detail |
| GET | `/teacher` | `teacher.dashboard` | Teacher overview (P2-E3) |
| GET | `/teacher/submissions` | `teacher.submissions.index` | Grading queue |
| GET | `/teacher/submissions/by-student` | `teacher.submissions.by-student` | Grouped by student |
| GET | `/teacher/submissions/{submission}` | `teacher.submissions.show` | Grade form |
| PUT | `/teacher/submissions/{submission}` | `teacher.submissions.update` | Save grade |
| Resource | `/admin/users` | `admin.users.*` | User CRUD (no show) |
| Resource | `/manage/quizzes` | `manage.quizzes.*` | Quiz CRUD (no show) |

---

### 5. Feature Inventory — Build Status

| Feature | Status | Notes |
|---------|--------|-------|
| Username/password auth | ✅ Done | |
| Role-based routing | ✅ Done | `CheckRole` middleware |
| Learn2Success marketing home + carousel | ✅ Done | |
| Student week journey + quiz path | ✅ Done | Phase 2 P2-E1 |
| Week unlock by registration date | ✅ Done | `WeekUnlockService` |
| Quiz time limit + auto-submit | ✅ Done | |
| Question-type tags (Choose / Speak) | ✅ Done | |
| Quiz detail & attempt history | ✅ Done | |
| Multi-attempt per quiz | ✅ Done | |
| In-progress guard | ✅ Done | |
| Recording + 30s limit + countdown | ✅ Done | `quiz-play.js` |
| TTS Listen | ✅ Done | Web Speech API |
| Audio upload & storage | ✅ Done | `AudioUploadService` |
| Multiple choice + recording questions | ✅ Done | Program seed uses Choose + Speak |
| Speaking pattern (admin) | ✅ Done | Supported in admin; not in program seed |
| Auto-grade all-MC quiz | ✅ Done | |
| Teacher grading queue & filters | ✅ Done | |
| Admin read-only submission review | ✅ Done | Phase 2 P2-E4 (Module 16) |
| Staff data tables (search/sort/filter) | ✅ Done | Phase 2 P2-E5 (Module 17) |
| Admin user + quiz CRUD | ✅ Done | |
| Pest automated tests | ✅ Done | 80+ tests (`php artisan test`) |
| Admin + teacher dashboards | ✅ Done | Phase 2 P2-E3 (Module 15) |
| Energy / lives gamification | ⬜ Not built | Out of scope P2-E1 |
| Admin week CRUD UI | ✅ Done | Phase 2 P2-E2 |
| Password reset / self-register | ⬜ Not built | Excluded MVP |
| Browser E2E tests | ⬜ Not built | |
| Native mobile app | ⬜ Not built | Excluded |

---

### 6. Implementation Phases (Actual)

#### Phase 1 ✅ Complete (Epics E1–E12)

Auth, roles, admin CRUD, quiz engine, student play, submissions, auto-grade, teacher grade, UI shell, branding. See `docs/phase-1-technical-design-and-tasks.md`.

#### Phase 2

| Epic | Name | Status |
|------|------|--------|
| P2-E1 | Week Management & Gamification Journey | ✅ Done |
| P2-E2 | Admin Week Management | ✅ Done |
| P2-E3 | Staff Dashboards (Admin + Teacher) | ✅ Done |
| P2-E4 | Admin Submission Review (Read-Only) | ✅ Done |
| P2-E5 | Staff Data Tables | ✅ Done |

See `docs/phase-2-technical-design-and-tasks.md` · `docs/epics/epic-16-admin-submission-review.md` · `docs/epics/epic-17-staff-data-tables.md`.

---

### 7. Success Metrics

- **Functional parity:** 100% of screen catalog flows work after rebuild without regression.
- **Domain rules:** Zero production incidents for duplicate in-progress attempts or over-max grading in first 30 days.
- **Student UX:** ≥90% of recording uploads succeed on first attempt on supported mobile browsers (Chrome/Safari current versions).
- **Teacher throughput:** Median time to grade a 5-question recording submission under 5 minutes (excluding listening time).
- **Quality gate:** ≥80% line coverage on submission, grading, and quiz validation services before production cutover.
- **Documentation:** PRD, use cases, and UML diagrams updated in the same PR as any schema or lifecycle change.

---

### 8. Related Documentation (Repository)

| Document | Purpose |
|----------|---------|
| `docs/learn2earn-prd.md` | This PRD — product source of truth |
| `docs/phase-1-technical-design-and-tasks.md` | Phase 1 architecture & epics |
| `docs/phase-2-technical-design-and-tasks.md` | Phase 2 architecture & epics |
| `docs/implementation-status.md` | Progress tracker |
| `docs/database-schema.md` | Table definitions |
| `docs/routes-reference.md` | Full route contract |
| `docs/epics/README.md` | Per-epic specs |
| `docs/flows/*` | Sequence diagrams + manual QA |
| `DESIGN.md` | Visual design tokens |

---

### 9. Understanding Phase Summary (Rebuild Rationale)

| Question | Answer |
|----------|--------|
| **Why is this being built?** | To run structured English speaking assessments: students practice and submit spoken (and selected) answers; teachers grade consistently; admins manage users and quizzes — replacing ad-hoc tools and an unmaintainable vibe-coded codebase. |
| **What is the development goal?** | Maintainable **Learn2Success** app with clear modules, enforced domain rules, services, tests, and PRD guardrails. |
| **What is the expected result?** | Phase 1 + Phase 2 (weeks + staff dashboards) shipped; documented flows; 65+ automated tests on critical paths. |
