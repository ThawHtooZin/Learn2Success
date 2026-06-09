<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'question_id' => Question::factory(),
        ];
    }
}
