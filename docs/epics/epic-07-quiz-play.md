# Epic 7 — Student Quiz Play & Response Capture

**Status:** ✅ Done
**Depends:** E6, E8, E9 (complete hooks E9)  
**PRD:** Module 7 · FR-S6–S11, S7–S9 · Screen 5

## Goal

Sequential play UI, audio recording, selection APIs, completion validation.

## Create

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Student/QuizPlayController.php` | play view |
| `app/Http/Controllers/Student/SubmissionAnswerController.php` | audio, selection, complete |
| `app/Http/Requests/Student/UploadAudioRequest.php` | MIME, max 50MB |
| `app/Http/Requests/Student/SaveSelectionRequest.php` | option indexes |
| `app/Services/Submissions/AudioUploadService.php` | store, replace file |
| `app/Services/Submissions/AnswerCompletionValidator.php` | all questions answered |
| `resources/views/student/quizzes/play.blade.php` | sequential UI |
| `resources/views/components/question/recording.blade.php` | |
| `resources/views/components/question/multiple-choice.blade.php` | |
| `resources/views/components/question/speaking-pattern.blade.php` | |
| `resources/js/recorder.js` | MediaRecorder, countdown, 30s cap |
| `resources/js/quiz-play.js` | Alpine progress, API calls |
| `tests/Feature/Student/QuizPlayTest.php` | |
| `tests/Feature/Student/SubmissionApiTest.php` | |

## Routes

| Method | Path | Name |
|--------|------|------|
| GET | `/student/quizzes/{quiz}/play` | `student.quizzes.play` |
| POST | `/student/submissions/{submission}/questions/{question}/audio` | `student.submissions.audio` |
| POST | `/student/submissions/{submission}/questions/{question}/selection` | `student.submissions.selection` |
| POST | `/student/submissions/{submission}/complete` | `student.submissions.complete` |

**All submission routes:** `SubmissionOwnershipGuard` + verify question belongs to submission's quiz.

## Recording UI (client)

- Optional **Listen** — `speechSynthesis`, `en-US`
- **Record** — 3-2-1 countdown, max **30 seconds**
- Stop, playback, re-record
- Upload on continue via `audio` API

## AudioUploadService

- Disk: `public`
- Path: `recordings/{submission_id}/{question_id}.{ext}`
- Allowed MIME: webm, mp3, m4a, wav, ogg, etc.
- Max: 50 MB
- Replace prior file on re-upload

## complete endpoint

1. Validate all answers have audio or selections (type-dependent)
2. Set `completed_at`, `duration_seconds`
3. Call `AutoGradingService` if all MC (E9) else leave `pending`
4. Return redirect to quiz detail

## Tests

- Ownership 403 on another student's submission
- Invalid MIME / oversized audio rejected
- Selection saved for MC / speaking_pattern
- Complete fails if any question unanswered
- Resume loads saved audio URL / selections

## Acceptance criteria

- [ ] Sequential progress indicators
- [ ] 30s recording cap in UI
- [ ] CSRF on forms; JSON APIs use web middleware + CSRF token
- [ ] storage:link documented for audio playback

## Flow doc to create

`docs/flows/phase-1-epic-7-quiz-play-sequence.md`
