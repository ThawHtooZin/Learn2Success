<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReorderWeekQuizzesRequest extends FormRequest
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
            'quiz_ids' => ['required', 'array', 'min:0'],
            'quiz_ids.*' => ['integer', 'exists:quizzes,id'],
        ];
    }
}
