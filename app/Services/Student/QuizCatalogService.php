<?php

namespace App\Services\Student;

use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;

class QuizCatalogService
{
    public function latestSubmission(User $student, Quiz $quiz): ?Submission
    {
        return Submission::query()
            ->where('user_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->latest('created_at')
            ->first();
    }

    public function statusLabel(?Submission $submission): string
    {
        if ($submission === null) {
            return 'Not started';
        }

        if ($submission->isInProgress()) {
            return 'In progress';
        }

        if ($submission->isGraded()) {
            return 'Graded';
        }

        return 'Pending';
    }
}
