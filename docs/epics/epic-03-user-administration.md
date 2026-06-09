# Epic 3 — User Administration (Admin)

**Status:** ✅ Done
**Depends:** E1, E2, E11  
**PRD:** Module 3 · FR-U1–U5 · Screens 9–11

## Goal

Admin CRUD for users (no show page); paginated list, create, edit, delete.

## Create

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Admin/UserController.php` | index, create, store, edit, update, destroy |
| `app/Http/Requests/Admin/StoreUserRequest.php` | username, password, password_confirmation, role |
| `app/Http/Requests/Admin/UpdateUserRequest.php` | username, role, optional password |
| `resources/views/admin/users/index.blade.php` | Paginated table |
| `resources/views/admin/users/create.blade.php` | Create form |
| `resources/views/admin/users/edit.blade.php` | Edit form |
| `resources/views/admin/users/_form.blade.php` | Shared fields |
| `tests/Feature/Admin/UserManagementTest.php` | CRUD + guards |

## Modify

| File | Change |
|------|--------|
| `routes/web.php` | `Route::resource('admin/users', ...)->except(['show'])` |
| `database/seeders/DatabaseSeeder.php` | Already calls UserSeeder |

## Routes

| Method | Path | Name |
|--------|------|------|
| GET | `/admin/users` | `admin.users.index` |
| GET | `/admin/users/create` | `admin.users.create` |
| POST | `/admin/users` | `admin.users.store` |
| GET | `/admin/users/{user}/edit` | `admin.users.edit` |
| PUT/PATCH | `/admin/users/{user}` | `admin.users.update` |
| DELETE | `/admin/users/{user}` | `admin.users.destroy` |

**Middleware:** `auth`, `role:admin`  
**Layout:** `layouts/staff`

## Validation

| Field | Create | Edit |
|-------|--------|------|
| username | required, unique | required, unique except self |
| password | required, min 8, confirmed | nullable, min 8, confirmed |
| role | required, in: student,teacher,admin | same |

## Business rules

- Delete blocked when `auth()->id() === $user->id` → 403 or error flash
- Password blank on edit → keep current password
- Hash password on create/update

## Tests

- Admin can list/create/edit/delete users
- Non-admin gets 403
- Self-delete blocked
- Duplicate username rejected
- Password confirmation required on create

## Acceptance criteria

- [ ] Pagination on index
- [ ] No `show` route/action
- [ ] All forms CSRF protected
- [ ] Staff layout with Users nav active

## Flow doc to create

`docs/flows/phase-1-epic-3-user-administration-sequence.md`
