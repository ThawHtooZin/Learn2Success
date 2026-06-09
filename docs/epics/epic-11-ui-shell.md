# Epic 11 — UI Shell & Responsive Layouts

**Status:** ✅ Done  
**Depends:** E1, E2  
**PRD:** Module 11 · NFR-U1, NFR-U2

## Implemented layouts

| Layout | Roles | Behavior |
|--------|-------|----------|
| `layouts/staff` | Teacher, Admin | Sidebar + top navbar; gold active nav |
| `layouts/student` | Student | **Full-width mobile-first**, bottom nav, page transitions |
| `layouts/guest` | Guest | Marketing + login |

## Files

- `resources/views/layouts/staff.blade.php`
- `resources/views/layouts/student.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/components/flash-messages.blade.php`
- `resources/css/app.css` — student buttons, journey path, transitions

## Acceptance criteria

- [x] Staff sidebar layout
- [x] Student full-width mobile shell + bottom nav
- [x] Flash banners
- [x] DESIGN.md gold/sky palette
