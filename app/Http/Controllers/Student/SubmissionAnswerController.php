<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SaveSelectionRequest;
use App\Http\Requests\Student\UploadAudioRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Submission;
use App\Services\Authorization\SubmissionOwnershipGuard;
use App\Services\Submissions\AudioUploadService;
use App\Services\Submissions\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionAnswerController extends Controller
{
    public function __construct(
        private readonly SubmissionOwnershipGuard $ownershipGuard,
        private readonly AudioUploadService $audioUpload,
        private readonly SubmissionService $submissionService,
    ) {}

    public function uploadAudio(
        UploadAudioRequest $request,
        Submission $submission,
        Question $question,
    ): JsonResponse {
        $this->ownershipGuard->authorize(Auth::user(), $submission);
        $this->assertQuestionBelongsToSubmissionQuiz($submission, $question);

        $answer = $this->findAnswer($submission, $question);
        $path = $this->audioUpload->store($answer, $request->file('audio'));

        return response()->json([
            'audio_url' => asset('storage/'.$path),
        ]);
    }

    public function saveSelection(
        SaveSelectionRequest $request,
        Submission $submission,
        Question $question,
    ): JsonResponse {
        $this->ownershipGuard->authorize(Auth::user(), $submission);
        $this->assertQuestionBelongsToSubmissionQuiz($submission, $question);

        $answer = $this->findAnswer($submission, $question);
        $answer->update([
            'selected_options' => array_values($request->validated('selected_options')),
        ]);

        return response()->json(['ok' => true]);
    }

    public function complete(Request $request, Submission $submission): JsonResponse
    {
        $this->ownershipGuard->authorize(Auth::user(), $submission);

        $duration = (int) $request->input('duration_seconds', 0);
        $timeExpired = $request->boolean('time_expired');
        $this->submissionService->complete($submission, max(0, $duration), $timeExpired);

        return response()->json([
            'redirect' => route('student.quizzes.show', $submission->quiz_id),
        ]);
    }

    private function findAnswer(Submission $submission, Question $question): Answer
    {
        return $submission->answers()
            ->where('question_id', $question->id)
            ->firstOrFail();
    }

    private function assertQuestionBelongsToSubmissionQuiz(Submission $submission, Question $question): void
    {
        abort_unless($question->quiz_id === $submission->quiz_id, 404);
    }
}
