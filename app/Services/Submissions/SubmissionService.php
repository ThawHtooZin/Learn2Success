<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionStatus;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use App\Services\Grading\AutoGradingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function __construct(
        private readonly AutoGradingService $autoGradingService,
        private readonly AnswerCompletionValidator $completionValidator,
    ) {}

    public function getInProgress(User $student, Quiz $quiz): ?Submission
    {
        return Submission::query()
            ->where('user_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->latest('id')
            ->first();
    }

    public function startTry(User $student, Quiz $quiz): Submission
    {
        $this->ensureCanStartNewTry($student, $quiz);

        return DB::transaction(function () use ($student, $quiz) {
            $submission = Submission::query()->create([
                'user_id' => $student->id,
                'quiz_id' => $quiz->id,
                'status' => SubmissionStatus::Pending,
                'started_at' => now(),
            ]);

            foreach ($quiz->questions as $question) {
                $submission->answers()->create([
                    'question_id' => $question->id,
                ]);
            }

            return $submission->load(['answers.question', 'quiz.questions']);
        });
    }

    public function ensureCanStartNewTry(User $student, Quiz $quiz): void
    {
        if ($this->getInProgress($student, $quiz) !== null) {
            throw ValidationException::withMessages([
                'submission' => 'You already have an in-progress attempt for this quiz.',
            ]);
        }
    }

    public function complete(Submission $submission, int $durationSeconds, bool $forceOnTimeout = false): Submission
    {
        $submission->load(['answers.question', 'quiz.questions']);

        if (! $forceOnTimeout) {
            $this->completionValidator->validate($submission);
        }

        return DB::transaction(function () use ($submission, $durationSeconds) {
            $submission->update([
                'completed_at' => now(),
                'duration_seconds' => $durationSeconds,
            ]);

            $submission = $submission->fresh(['answers.question', 'quiz.questions']);

            if ($this->autoGradingService->shouldAutoGrade($submission->quiz)) {
                $this->autoGradingService->grade($submission);
            }

            return $submission->fresh(['answers.question', 'quiz.questions']);
        });
    }
}
