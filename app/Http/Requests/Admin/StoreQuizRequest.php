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
            'questions.*.choices.*' => ['string'],
            'questions.*.correct_option_indexes' => ['nullable', 'array'],
            'questions.*.correct_option_indexes.*' => ['integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'time_limit_seconds' => $this->filled('time_limit_seconds') ? $this->integer('time_limit_seconds') : null,
        ]);
    }
}
