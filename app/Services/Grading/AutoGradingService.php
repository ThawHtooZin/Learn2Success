<?php

namespace App\Services\Grading;

use App\Enums\SubmissionStatus;
use App\Models\Quiz;
use App\Models\Submission;

class AutoGradingService
{
    public function shouldAutoGrade(Quiz $quiz): bool
    {
        return $quiz->isAllMultipleChoice();
    }

    public function grade(Submission $submission): void
    {
        if (! $this->shouldAutoGrade($submission->quiz)) {
            return;
        }

        $markPerQuestion = $submission->quiz->effectiveMarkPerQuestion();
        $total = 0;

        foreach ($submission->answers as $answer) {
            $correct = $this->optionsMatch(
                $answer->selected_options ?? [],
                $answer->question->correctOptionIndexes(),
            );

            $mark = $correct ? $markPerQuestion : 0;

            $answer->update([
                'is_auto_correct' => $correct,
                'mark' => $mark,
            ]);

            $total += $mark;
        }

        $submission->update([
            'status' => SubmissionStatus::Graded,
            'total_mark' => $total,
        ]);
    }

    /**
     * @param  list<int>  $selected
     * @param  list<int>  $correct
     */
    private function optionsMatch(array $selected, array $correct): bool
    {
        $selected = array_values(array_unique(array_map('intval', $selected)));
        $correct = array_values(array_unique(array_map('intval', $correct)));
        sort($selected);
        sort($correct);

        return $selected === $correct;
    }
}
