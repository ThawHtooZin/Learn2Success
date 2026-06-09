# Documentation Index — Learn2Success

**Product:** Learn2Success — English speaking practice & assessment (Laravel 13)

---

## Start here

| Doc | Purpose |
|-----|---------|
| [learn2earn-prd.md](learn2earn-prd.md) | **Product source of truth** (PRD) |
| [implementation-status.md](implementation-status.md) | What's done + how to run |
| [phase-1-technical-design-and-tasks.md](phase-1-technical-design-and-tasks.md) | Phase 1 architecture (E1–E12 ✅) |
| [phase-2-technical-design-and-tasks.md](phase-2-technical-design-and-tasks.md) | Phase 2 architecture (P2-E1 ✅) |

---

## Reference

| Doc | Purpose |
|-----|---------|
| [database-schema.md](database-schema.md) | Tables, columns, relationships |
| [routes-reference.md](routes-reference.md) | All HTTP routes (all ✅) |
| [epics/README.md](epics/README.md) | Per-epic implementation specs |
| [../DESIGN.md](../DESIGN.md) | Colors, typography, components |

---

## Flow docs (sequence + manual QA)

| Doc | Epic |
|-----|------|
| [flows/phase-1-epic-1-authentication-sequence.md](flows/phase-1-epic-1-authentication-sequence.md) | E1 |
| [flows/phase-1-epic-2-authorization-sequence.md](flows/phase-1-epic-2-authorization-sequence.md) | E2 |
| [flows/phase-2-epic-1-week-gamification-sequence.md](flows/phase-2-epic-1-week-gamification-sequence.md) | P2-E1 |

---

## Quick setup

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed && php artisan storage:link
npm run dev   # terminal 1
php artisan serve   # terminal 2
php artisan test   # 47 tests
```

**Seed logins:** `student` / `student_allweeks` / `teacher` / `admin` — password: `password`

---

## Current student experience

1. **Home** (`/student`) — vertical week journey (locked/unlocked nodes)
2. **Week** (`/student/weeks/{week}`) — quiz step path with Choose/Speak tags
3. **Quiz** — detail, play (Record on Speak questions), retakes allowed

Program seed: **4 weeks × 4 quizzes** — Quick Choose, Grammar Choose, Speak Up, Choose & Speak.
