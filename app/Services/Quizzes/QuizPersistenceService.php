<?php

namespace App\Services\Quizzes;

use App\Models\Quiz;
use App\Services\Questions\QuestionValidationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizPersistenceService
{
    public function __construct(
        private readonly QuestionValidationService $questionValidator,
    ) {}

    /**
     * @param  array<string, mixed>  $quizData
     * @param  list<array<string, mixed>>  $questions
     */
    public function create(array $quizData, array $questions): Quiz
    {
        $this->assertQuestionCount($questions);

        return DB::transaction(function () use ($quizData, $questions) {
            $quiz = Quiz::query()->create($quizData);
            $this->syncQuestions($quiz, $questions);
            $this->applyDefaultMarkPerQuestion($quiz);

            return $quiz->fresh(['questions']);
        });
    }

    /**
     * @param  array<string, mixed>  $quizData
     * @param  list<array<string, mixed>>  $questions
     */
    public function update(Quiz $quiz, array $quizData, array $questions): Quiz
    {
        $this->assertQuestionCount($questions);

        return DB::transaction(function () use ($quiz, $quizData, $questions) {
            $quiz->update($quizData);
            $quiz->questions()->delete();
            $this->syncQuestions($quiz, $questions);
            $this->applyDefaultMarkPerQuestion($quiz->fresh());

            return $quiz->fresh(['questions']);
        });
    }

    /**
     * @param  list<array<string, mixed>>  $questions
     */
    private function syncQuestions(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $index => $questionInput) {
            $validated = $this->questionValidator->validate($questionInput);

            $quiz->questions()->create([
                ...$validated,
                'sort_order' => $index,
            ]);
        }
    }

    private function applyDefaultMarkPerQuestion(Quiz $quiz): void
    {
        if ($quiz->mark_per_question && (float) $quiz->mark_per_question > 0) {
            return;
        }

        $count = $quiz->questions()->count();

        if ($count === 0) {
            return;
        }

        $quiz->update([
            'mark_per_question' => round($quiz->total_marks / $count, 2),
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $questions
     */
    private function assertQuestionCount(array $questions): void
    {
        $count = count($questions);

        if ($count < 1 || $count > 100) {
            throw ValidationException::withMessages([
                'questions' => 'A quiz must have between 1 and 100 questions.',
            ]);
        }
    }
}
