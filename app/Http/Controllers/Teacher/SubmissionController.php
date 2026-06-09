<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\UpdateGradingRequest;
use App\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\Grading\GradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly GradingService $gradingService,
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'ready');

        $query = Submission::query()
            ->with(['user', 'quiz'])
            ->latest('created_at');

        match ($filter) {
            'in_progress' => $query->whereNull('completed_at'),
            'graded' => $query->where('status', SubmissionStatus::Graded),
            'all' => null,
            default => $query->where('status', SubmissionStatus::Pending)->whereNotNull('completed_at'),
        };

        $submissions = $query->paginate(20)->withQueryString();

        return view('teacher.submissions.index', compact('submissions', 'filter'));
    }

    public function byStudent(): View
    {
        $students = User::query()
            ->where('role', UserRole::Student)
            ->whereHas('submissions')
            ->with(['submissions' => fn ($q) => $q->with('quiz')->latest('created_at')])
            ->orderBy('username')
            ->get();

        return view('teacher.submissions.by-student', compact('students'));
    }

    public function show(Submission $submission): View
    {
        $submission->load(['answers.question', 'quiz.week', 'quiz.questions', 'user']);

        $maxPerQuestion = $submission->quiz->effectiveMarkPerQuestion();

        return view('teacher.submissions.show', compact('submission', 'maxPerQuestion'));
    }

    public function update(UpdateGradingRequest $request, Submission $submission): RedirectResponse
    {
        $validated = $request->validated();
        $answerGrades = [];

        foreach ($validated['answers'] as $answerId => $data) {
            $answerGrades[(int) $answerId] = $data;
        }

        $this->gradingService->save(
            $submission,
            $answerGrades,
            $validated['teacher_feedback'] ?? null,
        );

        return redirect()
            ->route('teacher.submissions.show', $submission)
            ->with('success', 'Grading saved.');
    }
}
