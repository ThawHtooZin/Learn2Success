<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'total_marks' => ['required', 'integer', 'min:1'],
            'mark_per_question' => ['nullable', 'numeric', 'min:0'],
            'time_limit_seconds' => ['nullable', 'integer', 'min:1'],
            'questions' => ['required', 'array', 'min:1', 'max:100'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.question_type' => ['required', 'in:recording,multiple_choice'],
            'questions.*.choices' => ['nullable', 'array'],
            'questions.*.choices.*' => ['nullable', 'string'],
            'questions.*.correct_option_indexes' => ['nullable', 'array'],
            'questions.*.correct_option_indexes.*' => ['integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $questions = collect($this->input('questions', []))
            ->map(function (array $question): array {
                $type = $question['question_type'] ?? 'recording';

                if ($type === 'recording') {
                    unset($question['choices'], $question['correct_option_indexes'], $question['correct_option_index']);

                    return $question;
                }

                if (array_key_exists('correct_option_index', $question) && $question['correct_option_index'] !== null && $question['correct_option_index'] !== '') {
                    $question['correct_option_indexes'] = [(int) $question['correct_option_index']];
                }

                unset($question['correct_option_index']);

                return $question;
            })
            ->all();

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'mark_per_question' => $this->filled('mark_per_question') ? $this->input('mark_per_question') : null,
            'time_limit_seconds' => $this->filled('time_limit_seconds') ? $this->integer('time_limit_seconds') : null,
            'questions' => $questions,
        ]);
    }
}
