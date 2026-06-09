<?php

namespace App\Services\Grading;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradingService
{
    /**
     * @param  array<int, array{mark: mixed, teacher_feedback?: string|null}>  $answerGrades
     */
    public function save(Submission $submission, array $answerGrades, ?string $teacherFeedback): Submission
    {
        $submission->load(['answers', 'quiz']);

        $maxPerQuestion = $submission->quiz->effectiveMarkPerQuestion();
        $quizTotal = (float) $submission->quiz->total_marks;

        return DB::transaction(function () use ($submission, $answerGrades, $teacherFeedback, $maxPerQuestion, $quizTotal) {
            $total = 0;
            $errors = [];
            $pendingUpdates = [];

            foreach ($submission->answers as $answer) {
                $data = $answerGrades[$answer->id] ?? null;

                if ($data === null) {
                    continue;
                }

                $mark = (float) ($data['mark'] ?? 0);

                if ($mark > $maxPerQuestion) {
                    $errors["answers.{$answer->id}.mark"] = sprintf(
                        'Mark cannot exceed %.2f for this question.',
                        $maxPerQuestion,
                    );
                }

                $total += $mark;

                $pendingUpdates[$answer->id] = [
                    'mark' => $mark,
                    'teacher_feedback' => $data['teacher_feedback'] ?? null,
                ];
            }

            if ($total > $quizTotal) {
                $errors['total_mark'] = sprintf(
                    'Total marks cannot exceed the quiz maximum of %.2f.',
                    $quizTotal,
                );
            }

            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }

            foreach ($submission->answers as $answer) {
                if (! isset($pendingUpdates[$answer->id])) {
                    continue;
                }

                $answer->update($pendingUpdates[$answer->id]);
            }

            $submission->update([
                'status' => SubmissionStatus::Graded,
                'total_mark' => $total,
                'teacher_feedback' => $teacherFeedback,
            ]);

            return $submission->fresh(['answers.question', 'quiz', 'user']);
        });
    }
}
