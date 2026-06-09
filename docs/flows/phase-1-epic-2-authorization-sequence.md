# Phase 1, Epic 2 — Authorization & Role Access

## Sequence

```mermaid
sequenceDiagram
    actor User
    participant Route
    participant Auth
    participant CheckRole
    participant Controller

    User->>Route: Request protected route
    Route->>Auth: auth middleware
    alt Not logged in
        Auth-->>User: redirect to /login
    else Logged in
        Auth->>CheckRole: role middleware
        alt Wrong role
            CheckRole-->>User: 403 Forbidden
        else Correct role
            CheckRole->>Controller: continue
            Controller-->>User: 200 response
        end
    end
```

## Student submission ownership (API guard)

```mermaid
sequenceDiagram
    actor Student
    participant API
    participant Guard as SubmissionOwnershipGuard
    participant Submission

    Student->>API: POST student submission action
    API->>Submission: load submission
    API->>Guard: authorize(user, submission.user_id)
    alt Not owner or not student role
        Guard-->>Student: 403 Forbidden
    else Owner student
        Guard-->>API: allowed
        API-->>Student: continue handler
    end
```

## Route protection

| Route | Middleware | Roles |
|-------|------------|-------|
| `/student` | `auth`, `role:student` | student |
| `/teacher/submissions` | `auth`, `role:teacher` | teacher |
| `/manage/quizzes` | `auth`, `role:admin` | admin |

## Manual QA

1. Log in as `student` / `password`.
2. Visit `/student` — should load.
3. Visit `/teacher/submissions` — should show **403 Forbidden**.
4. Visit `/manage/quizzes` — should show **403 Forbidden**.
5. Log out. Log in as `teacher` / `password`.
6. Visit `/teacher/submissions` — should load.
7. Visit `/student` and `/manage/quizzes` — both **403**.
8. Log in as `admin` / `password`.
9. Visit `/manage/quizzes` — should load.
10. Visit `/student` and `/teacher/submissions` — both **403**.

## Notes

- `SubmissionOwnershipGuard` is wired into student submission APIs in Epic 7.
- `CheckRole` middleware alias: `role:student`, `role:teacher`, `role:admin`.
