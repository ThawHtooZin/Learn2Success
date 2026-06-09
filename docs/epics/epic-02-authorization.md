# Epic 2 — Authorization & Role Access

**Status:** ✅ Done  
**Depends:** E1  
**PRD:** Module 2 · FR-Z1–Z5 (Z4/Z5 wired in later epics)

## Goal

Enforce role separation on routes; foundation for student submission ownership.

## Implemented

| Item | Path |
|------|------|
| CheckRole middleware | `app/Http/Middleware/CheckRole.php` |
| Middleware alias | `bootstrap/app.php` → `role` |
| Ownership guard | `app/Services/Authorization/SubmissionOwnershipGuard.php` |
| Role route groups | `routes/web.php` |
| Tests | `tests/Feature/Authorization/RoleAccessTest.php`, `tests/Unit/Authorization/SubmissionOwnershipGuardTest.php` |
| Flow doc | `docs/flows/phase-1-epic-2-authorization-sequence.md` |

## Route middleware

| Prefix | Middleware |
|--------|------------|
| `/student` | `auth`, `role:student` |
| `/teacher/*` | `auth`, `role:teacher` |
| `/manage/*`, `/admin/*` | `auth`, `role:admin` |

## Acceptance criteria

- [x] Wrong role → 403
- [x] `SubmissionOwnershipGuard` rejects non-owner / non-student
- [ ] Inactive quiz → 404 (E6)
- [ ] Ownership on submission APIs (E7)
