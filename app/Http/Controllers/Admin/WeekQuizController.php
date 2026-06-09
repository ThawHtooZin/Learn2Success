<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignWeekQuizRequest;
use App\Http\Requests\Admin\ReorderWeekQuizzesRequest;
use App\Models\Quiz;
use App\Models\Week;
use App\Services\Weeks\WeekQuizAssignmentService;
use Illuminate\Http\JsonResponse;

class WeekQuizController extends Controller
{
    public function __construct(
        private readonly WeekQuizAssignmentService $assignmentService,
    ) {}

    public function store(AssignWeekQuizRequest $request, Week $week): JsonResponse
    {
        $quiz = Quiz::query()->findOrFail($request->integer('quiz_id'));

        if ($quiz->week_id === $week->id) {
            return response()->json(['message' => 'Quiz is already in this week.'], 422);
        }

        $this->assignmentService->assign($week, $quiz);

        $quiz->loadCount('questions');

        return response()->json([
            'quiz' => $this->quizPayload($quiz),
        ]);
    }

    public function reorder(ReorderWeekQuizzesRequest $request, Week $week): JsonResponse
    {
        $this->assignmentService->reorder($week, $request->input('quiz_ids'));

        return response()->json(['message' => 'Order saved.']);
    }

    public function destroy(Week $week, Quiz $quiz): JsonResponse
    {
        $this->assignmentService->remove($week, $quiz);

        return response()->json(['message' => 'Quiz removed from week.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function quizPayload(Quiz $quiz): array
    {
        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'questions_count' => $quiz->questions_count ?? $quiz->questions()->count(),
            'time_limit_seconds' => $quiz->time_limit_seconds,
            'edit_url' => route('manage.quizzes.edit', $quiz),
        ];
    }
}
