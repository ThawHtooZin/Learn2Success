# Learn2Success

English speaking practice & assessment platform — Laravel 13, Tailwind v4, Alpine.js.

## Docs

All product and technical documentation lives in **[docs/](docs/README.md)**.

| Doc | Description |
|-----|-------------|
| [PRD](docs/learn2earn-prd.md) | Product requirements |
| [Implementation status](docs/implementation-status.md) | Setup, seed accounts, test count |
| [Phase 1 design](docs/phase-1-technical-design-and-tasks.md) | Epics E1–E12 |
| [Phase 2 design](docs/phase-2-technical-design-and-tasks.md) | Week gamification (P2-E1) |

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run dev
php artisan serve
```

## Test accounts

| Username | Password | Role |
|----------|----------|------|
| student | password | Student (Week 1 unlocked) |
| student_allweeks | password | Student (all weeks) |
| teacher | password | Teacher |
| admin | password | Admin |

## Tests

```bash
php artisan test   # 47 tests
```

## Stack

- Laravel 13, PHP 8.3+
- SQLite (dev) / MySQL (prod)
- Blade + Tailwind CSS v4 + Alpine.js
- Quicksand font, gold/sky design system (`DESIGN.md`)
