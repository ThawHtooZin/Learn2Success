# Phase 2, Epic 4 — Admin Submission Review (Read-Only)

**Status:** ✅ Done  
**Depends:** Phase 1 E10 (Teacher Grading), P2-E3 (Staff Dashboards), P2-E5 (Staff Data Tables)  
**PRD:** Module 16 (Admin Submission Review)

---

## Goal

Admins can **see** submission and grade details for oversight, but **only teachers** can enter or change marks and feedback.

---

## Why / What / Result

| | |
|---|---|
| **Why** | Admins need program visibility into student attempts and teacher grades without mixing grading duties into admin workflows. |
| **What** | Admin routes at `/admin/submissions` mirroring the teacher list, by-student, and detail views — shared Blade under `staff/submissions/` with `$canGrade` flag. |
| **Result** | Admin sidebar **Submissions** link; read-only detail with “View only” banner; teacher grading unchanged on `/teacher/submissions`. |

---

## Role rules

| Action | Admin | Teacher |
|--------|-------|---------|
| List / filter submissions | ✅ | ✅ |
| By-student view | ✅ | ✅ |
| View answers, audio, marks, feedback | ✅ | ✅ |
| Enter / save marks & feedback | ❌ | ✅ |

---

## Architecture

```
GET /admin/submissions
  → Admin\SubmissionController@index
  → SubmissionListService::index()
  → staff.submissions.index (canGrade=false)

GET /admin/submissions/{submission}
  → Admin\SubmissionController@show
  → staff.submissions.show (read-only, no form)

PUT /teacher/submissions/{submission}
  → Teacher only (role:teacher middleware)
```

**Shared service:** `App\Services\Submissions\SubmissionListService`  
**Shared views:** `resources/views/staff/submissions/*`

---

## Routes

| Method | Path | Name | Role |
|--------|------|------|------|
| GET | `/admin/submissions` | `admin.submissions.index` | Admin |
| GET | `/admin/submissions/by-student` | `admin.submissions.by-student` | Admin |
| GET | `/admin/submissions/{submission}` | `admin.submissions.show` | Admin |

Teacher routes unchanged (`teacher.submissions.*`).

---

## UI

| Screen | Admin behaviour |
|--------|-----------------|
| **Index** | Same data table as teacher; action link says **View** |
| **By student** | Grouped attempts; **View** per row |
| **Detail** | Full quiz meta + answers; marks/feedback as text; **View only** badge; no Save button |

Admin dashboard recent activity links to `admin.submissions.show`.

---

## Tests

| File | Covers |
|------|--------|
| `tests/Feature/Admin/SubmissionReviewTest.php` | Admin list/show, read-only UI, 403 on teacher PUT |
| `tests/Feature/Authorization/RoleAccessTest.php` | Admin can access admin submission routes; cannot access teacher routes |

---

## Flow doc

[docs/flows/phase-2-epic-4-admin-submission-review-sequence.md](../flows/phase-2-epic-4-admin-submission-review-sequence.md)
