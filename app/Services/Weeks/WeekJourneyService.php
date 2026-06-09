<?php

namespace App\Services\Weeks;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Week;
use App\Services\Student\QuizCatalogService;
use Illuminate\Support\Collection;

class WeekJourneyService
{
    public function __construct(
        private readonly WeekUnlockService $unlock,
        private readonly QuizCatalogService $catalog,
    ) {}

    /**
     * @return Collection<int, array{
     *     week: Week,
     *     unlocked: bool,
     *     unlocks_at: \Carbon\CarbonInterface,
     *     days_until: int,
     *     completed: bool,
     *     progress_percent: int,
     *     quiz_count: int,
     *     completed_count: int,
     * }>
     */
    public function weekMap(User $student): Collection
    {
        $weeks = Week::query()
            ->active()
            ->withCount(['quizzes' => fn ($q) => $q->active()])
            ->orderBy('sort_order')
            ->get();

        return $weeks->map(function (Week $week) use ($student) {
            $unlocked = $this->unlock->isUnlocked($student, $week);
            $quizzes = $week->quizzes()->active()->withCount('questions')->get();
            $completedCount = $quizzes->filter(
                fn (Quiz $quiz) => $this->catalog->latestSubmission($student, $quiz)?->isGraded()
            )->count();
            $quizCount = $quizzes->count();

            return [
                'week' => $week,
                'unlocked' => $unlocked,
                'unlocks_at' => $this->unlock->unlocksAt($student, $week),
                'days_until' => $this->unlock->daysUntilUnlock($student, $week),
                'completed' => $quizCount > 0 && $completedCount === $quizCount,
                'progress_percent' => $quizCount > 0 ? (int) round(($completedCount / $quizCount) * 100) : 0,
                'quiz_count' => $quizCount,
                'completed_count' => $completedCount,
            ];
        });
    }

    /**
     * @return Collection<int, array{
     *     quiz: Quiz,
     *     step: int,
     *     status: string,
     *     latest: \App\Models\Submission|null,
     *     in_progress: \App\Models\Submission|null,
     *     is_current: bool,
     * }>
     */
    public function quizSteps(User $student, Week $week): Collection
    {
        $quizzes = $week->quizzes()
            ->active()
            ->withCount('questions')
            ->with('questions:id,quiz_id,question_type')
            ->orderBy('sort_order_in_week')
            ->get();

        $currentIndex = null;

        foreach ($quizzes as $index => $quiz) {
            $latest = $this->catalog->latestSubmission($student, $quiz);
            if ($latest === null || $latest->isInProgress()) {
                $currentIndex = $index;
                break;
            }
        }

        return $quizzes->values()->map(function (Quiz $quiz, int $index) use ($student, $currentIndex) {
            $latest = $this->catalog->latestSubmission($student, $quiz);
            $inProgress = $latest?->isInProgress() ? $latest : null;

            return [
                'quiz' => $quiz,
                'step' => $index + 1,
                'status' => $this->catalog->statusLabel($latest),
                'latest' => $latest,
                'in_progress' => $inProgress,
                'is_current' => $currentIndex === $index,
            ];
        });
    }
}
