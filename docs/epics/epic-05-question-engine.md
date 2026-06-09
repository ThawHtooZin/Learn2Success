# Epic 5 — Question Engine

**Status:** ✅ Done
**Depends:** E2  
**PRD:** Module 5 · FR-QT1–QT5

## Goal

Question types, meta JSON structure, and validation service used by quiz admin forms.

## Create

| File | Purpose |
|------|---------|
| `app/Enums/QuestionType.php` | `recording`, `multiple_choice`, `speaking_pattern` |
| `app/Data/QuestionMeta.php` | DTO: `choices[]`, `correct_option_indexes[]` |
| `app/Services/Questions/QuestionValidationService.php` | All type rules |
| `tests/Unit/Questions/QuestionValidationServiceTest.php` | Per-type validation |

## Domain rules

| Type | meta | Validation |
|------|------|------------|
| `recording` | empty or null meta | No choices required |
| `multiple_choice` | choices ≥ 2, exactly 1 correct index | Index in range |
| `speaking_pattern` | choices ≥ 2, ≥ 1 correct indexes | Indexes in range, unique |

## QuestionValidationService API

```php
validate(array $questionInput): void  // throws ValidationException
normalizeMeta(QuestionType $type, array $raw): array
```

## Input shape (from admin form)

```php
[
    'question_text' => string,
    'question_type' => string,
    'choices' => string[],           // optional for recording
    'correct_option_indexes' => int[],
]
```

## Tests (unit)

- MC: rejects 0 or 2+ correct indexes
- MC: rejects < 2 choices
- Speaking pattern: rejects 0 correct
- Rejects out-of-range correct index
- Recording: passes without choices

## Acceptance criteria

- [ ] All three types defined in enum
- [ ] Meta JSON schema documented and validated
- [ ] Service has full unit test coverage for rules
- [ ] No UI in this epic — service only (consumed by E4)

## Flow doc to create

`docs/flows/phase-1-epic-5-question-engine-sequence.md`
