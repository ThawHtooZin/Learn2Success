<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Services\Submissions\SubmissionListService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionListService $submissionList,
    ) {}

    public function index(Request $request): View
    {
        return view('staff.submissions.index', [
            ...$this->submissionList->index($request),
            'canGrade' => false,
            'indexRoute' => 'admin.submissions.index',
            'showRoute' => 'admin.submissions.show',
            'byStudentRoute' => 'admin.submissions.by-student',
            'pageTitle' => 'Submissions',
            'trailRoot' => ['label' => 'Submissions'],
        ]);
    }

    public function byStudent(): View
    {
        return view('staff.submissions.by-student', [
            'students' => $this->submissionList->studentsWithSubmissions(),
            'canGrade' => false,
            'showRoute' => 'admin.submissions.show',
            'indexRoute' => 'admin.submissions.index',
            'pageTitle' => 'Submissions by student',
            'trailRoot' => [
                'label' => 'Submissions',
                'url' => route('admin.submissions.index'),
            ],
        ]);
    }

    public function show(Submission $submission): View
    {
        return view('staff.submissions.show', [
            ...$this->submissionList->show($submission),
            'canGrade' => false,
            'indexRoute' => 'admin.submissions.index',
            'pageTitle' => 'View submission',
            'trailRoot' => [
                'label' => 'Submissions',
                'url' => route('admin.submissions.index'),
            ],
        ]);
    }
}
