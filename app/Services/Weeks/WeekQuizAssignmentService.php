<?php

namespace App\Services\Weeks;

use App\Models\Quiz;
use App\Models\Week;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WeekQuizAssignmentService
{
    public function assign(Week $week, Quiz $quiz): void
    {
        $maxOrder = $week->quizzes()->max('sort_order_in_week');
        $nextOrder = $maxOrder === null ? 0 : ((int) $maxOrder + 1);

        $quiz->update([
            'week_id' => $week->id,
            'sort_order_in_week' => $nextOrder,
        ]);
    }

    /**
     * @param  list<int>  $quizIds
     */
    public function reorder(Week $week, array $quizIds): void
    {
        $existingIds = $week->quizzes()->pluck('id')->sort()->values()->all();
        $requestedIds = collect($quizIds)->sort()->values()->all();

        if ($existingIds !== $requestedIds) {
            throw ValidationException::withMessages([
                'quiz_ids' => 'Quiz list does not match this week.',
            ]);
        }

        DB::transaction(function () use ($week, $quizIds) {
            foreach ($quizIds as $index => $quizId) {
                Quiz::query()
                    ->where('week_id', $week->id)
                    ->whereKey($quizId)
                    ->update(['sort_order_in_week' => $index]);
            }
        });
    }

    public function remove(Week $week, Quiz $quiz): void
    {
        if ($quiz->week_id !== $week->id) {
            throw ValidationException::withMessages([
                'quiz' => 'This quiz is not assigned to this week.',
            ]);
        }

        DB::transaction(function () use ($week, $quiz) {
            $quiz->update([
                'week_id' => null,
                'sort_order_in_week' => 0,
            ]);

            $week->quizzes()
                ->orderBy('sort_order_in_week')
                ->get()
                ->each(fn (Quiz $remaining, int $index) => $remaining->update([
                    'sort_order_in_week' => $index,
                ]));
        });
    }
}
