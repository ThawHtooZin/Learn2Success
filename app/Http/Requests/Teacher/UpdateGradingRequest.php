<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradingRequest extends FormRequest
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
            'teacher_feedback' => ['nullable', 'string'],
            'answers' => ['required', 'array'],
            'answers.*.mark' => ['required', 'numeric', 'min:0'],
            'answers.*.teacher_feedback' => ['nullable', 'string'],
        ];
    }
}
