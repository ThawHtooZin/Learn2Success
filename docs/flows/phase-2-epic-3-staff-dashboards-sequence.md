# Phase 2, Epic 3 — Staff Dashboards

## Sequence

```mermaid
sequenceDiagram
    actor Staff as Admin or Teacher
    participant Auth as LoginController
    participant User as User::homeRoute()
    participant Dash as DashboardController
    participant Svc as StaffDashboardService
    participant DB as Database
    participant View as dashboard.blade.php
    participant JS as staff-dashboard.js

    Staff->>Auth: POST /login
    Auth->>User: resolve home route
    User-->>Auth: /admin or /teacher
    Auth-->>Staff: redirect to dashboard

    Staff->>Dash: GET /admin or /teacher
    Dash->>Svc: adminOverview() or teacherOverview()
    Svc->>DB: aggregate users, quizzes, weeks, submissions
    DB-->>Svc: KPI + chart datasets + lists
    Svc-->>Dash: overview array
    Dash->>View: render with @json chart data
    View-->>Staff: HTML + staff-dashboard-data script
    Staff->>JS: DOMContentLoaded
    JS->>JS: Chart.js render line / doughnut / bar
```

## Manual QA

### Setup

```bash
php artisan migrate:fresh --seed
npm run build   # or npm run dev
php artisan serve
```

### Admin dashboard

1. Login `admin` / `password` → lands on `/admin`.
2. Confirm KPI cards: students, teachers, active quizzes, pending grades.
3. Confirm three charts render (line, donut, bar).
4. Click **Create week** quick action → week create form.
5. Recent activity shows seeded submissions (if any) with **View quiz** links.

### Teacher dashboard

1. Login `teacher` / `password` → lands on `/teacher`.
2. Confirm **Ready to grade** KPI matches `/teacher/submissions?filter=ready` count.
3. Priority queue lists oldest pending submissions with **Grade** buttons.
4. Click **Grade** → grade screen loads.
5. Charts render without console errors.

### Cross-role

1. As `teacher`, open `/admin` → **403**.
2. As `admin`, open `/teacher` → **403**.

### Mobile

1. Resize to phone width — KPI cards stack; charts remain readable.

### Automated

```bash
php artisan test --filter=DashboardTest
```
