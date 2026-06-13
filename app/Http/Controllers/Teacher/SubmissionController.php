<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\UpdateGradingRequest;
use App\Models\Submission;
use App\Services\Grading\GradingService;
use App\Services\Submissions\SubmissionListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly GradingService $gradingService,
        private readonly SubmissionListService $submissionList,
    ) {}

    public function index(Request $request): View
    {
        return view('staff.submissions.index', [
            ...$this->submissionList->index($request),
            'canGrade' => true,
            'indexRoute' => 'teacher.submissions.index',
            'showRoute' => 'teacher.submissions.show',
            'byStudentRoute' => 'teacher.submissions.by-student',
            'pageTitle' => 'Submissions',
            'trailRoot' => ['label' => 'Grade'],
        ]);
    }

    public function byStudent(): View
    {
        return view('staff.submissions.by-student', [
            'students' => $this->submissionList->studentsWithSubmissions(),
            'canGrade' => true,
            'showRoute' => 'teacher.submissions.show',
            'indexRoute' => 'teacher.submissions.index',
            'pageTitle' => 'Submissions by student',
            'trailRoot' => [
                'label' => 'Grade',
                'url' => route('teacher.submissions.index'),
            ],
        ]);
    }

    public function show(Submission $submission): View
    {
        return view('staff.submissions.show', [
            ...$this->submissionList->show($submission),
            'canGrade' => true,
            'indexRoute' => 'teacher.submissions.index',
            'pageTitle' => 'Grade submission',
            'trailRoot' => [
                'label' => 'Grade',
                'url' => route('teacher.submissions.index'),
            ],
        ]);
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
