# Phase 2, Epic 3 — Staff Dashboards (Admin + Teacher)

**Status:** ✅ Done  
**Depends:** Phase 1 complete, P2-E1, P2-E2  
**PRD:** Module 15 (Staff Dashboards)

---

## Goal

Give **admin** and **teacher** a beautiful **home dashboard** after login — at-a-glance KPIs, charts, priority actions, and recent activity. Replace landing on raw list pages (`/manage/quizzes`, `/teacher/submissions`).

**One epic, two dashboards** — shared layout patterns and one analytics service; role-specific metrics and CTAs.

**Not in scope (P2-E3):**

- CSV/PDF export
- Real-time websockets
- Per-teacher assignment (all teachers see the same queue today)
- Student-facing dashboard changes
- Custom date-range picker (fixed presets only: 7 / 14 / 30 days)

---

## Why / What / Result

| | |
|---|---|
| **Why** | Staff currently land on tables with no overview. Admins cannot see program health; teachers must hunt for what needs grading. |
| **What** | Role dashboards with stat cards, 2–3 charts, quick actions, and “needs attention” lists. Mobile-friendly staff layout. |
| **Result** | Login → dashboard. Sidebar **Dashboard** is first link. Grading and CRUD remain one click away. |

---

## UX principles

1. **Scan in 5 seconds** — largest number = most important action (pending grades for teacher; pending + students for admin).
2. **Whitespace** — card grid, no dense tables on the dashboard hero.
3. **Brand** — gold `#785900`, sky `#006399`, green `#006e1c`, soft `#E3F2FD` borders (`DESIGN.md`).
4. **Charts stay readable on mobile** — stack vertically; touch-friendly legend; no hover-only critical info.
5. **Empty states** — friendly copy when seed data has no submissions yet.
6. **No new chrome** — reuse `layouts/staff`, `<x-staff-nav-trail>` not needed (top-level page).

---

## Admin dashboard (`/admin`)

### Layout (top → bottom)

| Section | Content |
|---------|---------|
| **Header** | “Good morning, {username}” + today’s date + subtitle “Program overview” |
| **KPI row (4 cards)** | Students · Teachers · Active quizzes · **Pending grades** (completed, not graded) |
| **Charts row** | **Submissions over time** (last 14 days, line/area) · **Grading status** (pending / in progress / graded donut) |
| **Second chart row** | **Attempts by week** (bar — count of completed submissions per `quiz.week_id`) |
| **Quick actions** | Create user · Create quiz · Create week · View grading queue (links to existing routes) |
| **Recent activity** | Last 8 submissions: student, quiz, status chip, relative time, link to grade if applicable |

### Admin-only metrics (from existing tables)

- User counts by `role`
- `Quiz::active()` count; total quizzes
- `Week::` count (active weeks)
- `Submission` aggregates: pending (completed + status pending), in progress (`completed_at` null), graded
- Daily submission counts (`created_at` or `completed_at` — use **completed** for chart)
- Average `total_mark` on graded submissions (optional small stat under KPI)

---

## Teacher dashboard (`/teacher`)

### Layout

| Section | Content |
|---------|---------|
| **Header** | “Welcome, {username}” + **{N} ready to grade** as primary callout |
| **KPI row (4 cards)** | Ready to grade · Graded (last 7 days) · In progress · Active students (submitted in last 30 days) |
| **Charts row** | **Grading workload** (pending count per day, last 14 days) · **Status split** (donut: ready / in progress / graded) |
| **Priority queue** | Top 5 oldest **ready to grade** submissions — student, quiz, waiting since, **Grade** button |
| **Quick actions** | Full queue · By student |

Teacher sees **platform-wide** submission data (same as today’s grading list) — no ownership filter change in this epic.

---

## Charts (implementation)

- **Library:** [Chart.js](https://www.chartjs.org/) v4 via npm, initialized in `resources/js/staff-dashboard.js` (Alpine-friendly, destroy on navigate if needed).
- **Server:** JSON chart datasets built in `StaffDashboardService` — no client-side aggregation.
- **Accessibility:** `aria-label` on canvas wrapper; KPI cards duplicate key numbers in text.
- **Performance:** Single service method per dashboard; eager limits on recent lists; chart queries use `whereDate` + `groupBy` — no N+1.

---

## Architecture

```
GET /admin  → AdminDashboardController@index
GET /teacher → TeacherDashboardController@index
  → StaffDashboardService
      → adminOverview() | teacherOverview()
  → Blade + chart JSON @js() + staff-dashboard.js
```

### New files

| File | Purpose |
|------|---------|
| `app/Services/Dashboard/StaffDashboardService.php` | All metrics + chart dataset builders |
| `app/Http/Controllers/Admin/DashboardController.php` | Admin dashboard view |
| `app/Http/Controllers/Teacher/DashboardController.php` | Teacher dashboard view |
| `resources/views/admin/dashboard.blade.php` | Admin UI |
| `resources/views/teacher/dashboard.blade.php` | Teacher UI |
| `resources/views/components/dashboard/stat-card.blade.php` | Reusable KPI card |
| `resources/views/components/dashboard/chart-panel.blade.php` | Card wrapper + canvas |
| `resources/js/staff-dashboard.js` | Chart.js init |
| `tests/Feature/Admin/DashboardTest.php` | Admin auth + key metrics present |
| `tests/Feature/Teacher/DashboardTest.php` | Teacher auth + queue section |

### Modify

| File | Change |
|------|--------|
| `routes/web.php` | `GET /admin`, `GET /teacher` |
| `app/Models/User.php` | `homeRoute()` → dashboard routes |
| `resources/views/layouts/staff.blade.php` | **Dashboard** first nav item; highlight `admin.dashboard` / `teacher.dashboard` |
| `resources/js/app.js` | Import `staff-dashboard.js` |
| `vite.config.js` | Optional separate entry if lazy-load desired (default: bundle in app.js) |
| `docs/routes-reference.md` | New routes |

---

## Routes

| Method | Path | Name | Middleware |
|--------|------|------|------------|
| GET | `/admin` | `admin.dashboard` | auth, role:admin |
| GET | `/teacher` | `teacher.dashboard` | auth, role:teacher |

**Redirects (optional polish):** none required — old list URLs unchanged.

**Login redirect:** `User::homeRoute()` → dashboards.

---

## Acceptance criteria

- [x] Admin login lands on `/admin` dashboard with 4 KPI cards and 3 charts
- [x] Teacher login lands on `/teacher` dashboard with priority grade list
- [x] Sidebar **Dashboard** link active on dashboard routes
- [x] Charts render with seeded data (`migrate:fresh --seed`)
- [x] Empty DB shows zero-state copy, no JS errors
- [x] Mobile: cards stack; charts scroll horizontally only if needed
- [x] Teacher/admin cannot access each other’s dashboard (403)
- [x] Feature tests pass; full suite green
- [x] Flow doc: `docs/flows/phase-2-epic-3-staff-dashboards-sequence.md`

---

## Manual QA

1. `php artisan migrate:fresh --seed`
2. Login `admin` / `password` → see student count, week bar chart, recent submissions
3. Login `teacher` / `password` → see ready-to-grade count matches `/teacher/submissions?filter=ready`
4. Resize to mobile — layout readable
5. Complete a student submission → admin pending KPI increments; appears in teacher priority list

---

## Flow doc (on completion)

`docs/flows/phase-2-epic-3-staff-dashboards-sequence.md`
