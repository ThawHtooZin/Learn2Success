<?php

namespace App\Services\Questions;

use App\Enums\QuestionType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class QuestionValidationService
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{question_text: string, question_type: string, meta: array<string, mixed>|null}
     */
    public function validate(array $input): array
    {
        $validator = Validator::make($input, [
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:recording,multiple_choice'],
            'choices' => ['nullable', 'array'],
            'choices.*' => ['string'],
            'correct_option_indexes' => ['nullable', 'array'],
            'correct_option_indexes.*' => ['integer', 'min:0'],
        ]);

        $validator->validate();

        $type = QuestionType::from($input['question_type']);
        $meta = $this->normalizeMeta($type, $input);

        return [
            'question_text' => $input['question_text'],
            'question_type' => $type->value,
            'meta' => $meta,
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>|null
     */
    public function normalizeMeta(QuestionType $type, array $input): ?array
    {
        if ($type === QuestionType::Recording) {
            return null;
        }

        $choices = array_values(array_filter(
            $input['choices'] ?? [],
            fn ($c) => is_string($c) && trim($c) !== '',
        ));

        if (count($choices) < 2) {
            throw ValidationException::withMessages([
                'choices' => 'Multiple choice questions require at least 2 options.',
            ]);
        }

        $correctIndexes = array_values(array_unique(array_map(
            'intval',
            $input['correct_option_indexes'] ?? [],
        )));

        $maxIndex = count($choices) - 1;

        foreach ($correctIndexes as $index) {
            if ($index < 0 || $index > $maxIndex) {
                throw ValidationException::withMessages([
                    'correct_option_indexes' => 'Correct option index is out of range.',
                ]);
            }
        }

        if (count($correctIndexes) !== 1) {
            throw ValidationException::withMessages([
                'correct_option_indexes' => 'Multiple choice requires exactly one correct option.',
            ]);
        }

        return [
            'choices' => $choices,
            'correct_option_indexes' => $correctIndexes,
        ];
    }
}
