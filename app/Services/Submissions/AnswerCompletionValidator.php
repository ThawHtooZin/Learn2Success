<?php

namespace App\Services\Submissions;

use App\Models\Submission;
use Illuminate\Validation\ValidationException;

class AnswerCompletionValidator
{
    public function validate(Submission $submission): void
    {
        foreach ($submission->answers as $answer) {
            if (! $answer->isAnswered()) {
                throw ValidationException::withMessages([
                    'submission' => 'Please answer all questions before submitting.',
                ]);
            }
        }
    }
}
