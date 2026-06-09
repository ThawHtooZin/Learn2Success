<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'question_text' => fake()->sentence().'?',
            'question_type' => QuestionType::Recording,
            'meta' => null,
            'sort_order' => 0,
        ];
    }

    public function multipleChoice(): static
    {
        return $this->state(fn () => [
            'question_type' => QuestionType::MultipleChoice,
            'meta' => [
                'choices' => ['Option A', 'Option B', 'Option C'],
                'correct_option_indexes' => [0],
            ],
        ]);
    }

    public function recording(): static
    {
        return $this->state(fn () => [
            'question_type' => QuestionType::Recording,
            'meta' => null,
        ]);
    }
}
