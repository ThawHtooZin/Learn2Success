<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Services\Student\QuizCatalogService;
use App\Services\Submissions\SubmissionService;
use App\Services\Weeks\WeekUnlockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizCatalogService $catalog,
        private readonly SubmissionService $submissionService,
        private readonly WeekUnlockService $weekUnlock,
    ) {}

    private function assertQuizAccessible(Quiz $quiz): void
    {
        abort_unless($quiz->is_active, 404);

        if ($quiz->week_id === null) {
            return;
        }

        $quiz->loadMissing('week');
        abort_if($quiz->week === null, 404);
        $this->weekUnlock->assertUnlocked(Auth::user(), $quiz->week);
    }

    public function show(Quiz $quiz): View
    {
        $this->assertQuizAccessible($quiz);

        $quiz->loadCount('questions');
        $quiz->load('questions:id,quiz_id,question_type');
        $student = Auth::user();
        $attempts = $student->submissions()
            ->where('quiz_id', $quiz->id)
            ->with('quiz')
            ->latest('created_at')
            ->get();

        $inProgress = $this->submissionService->getInProgress($student, $quiz);

        return view('student.quizzes.show', compact('quiz', 'attempts', 'inProgress'));
    }

    public function startNewTry(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->assertQuizAccessible($quiz);

        $request->session()->put('quiz_new_try_'.$quiz->id, true);

        return redirect()->route('student.quizzes.play', ['quiz' => $quiz, 'new' => 1]);
    }

    public function play(Request $request, Quiz $quiz): View|RedirectResponse
    {
        $this->assertQuizAccessible($quiz);

        $student = Auth::user();
        $quiz->load('questions');

        $inProgress = $this->submissionService->getInProgress($student, $quiz);

        if ($inProgress) {
            $submission = $inProgress->load(['answers.question', 'quiz.questions']);

            return view('student.quizzes.play', compact('quiz', 'submission'));
        }

        if ($request->boolean('new')) {
            if (! $request->session()->pull('quiz_new_try_'.$quiz->id)) {
                return redirect()->route('student.quizzes.show', $quiz);
            }

            $submission = $this->submissionService->startTry($student, $quiz);

            return view('student.quizzes.play', compact('quiz', 'submission'));
        }

        if ($request->boolean('start')) {
            $hasAttempts = $student->submissions()->where('quiz_id', $quiz->id)->exists();

            if ($hasAttempts) {
                return redirect()->route('student.quizzes.show', $quiz);
            }

            $submission = $this->submissionService->startTry($student, $quiz);

            return view('student.quizzes.play', compact('quiz', 'submission'));
        }

        return redirect()->route('student.quizzes.show', $quiz);
    }
}
