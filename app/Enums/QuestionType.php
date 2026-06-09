<?php

namespace App\Enums;

enum QuestionType: string
{
    case Recording = 'recording';
    case MultipleChoice = 'multiple_choice';

    public function requiresChoices(): bool
    {
        return $this === self::MultipleChoice;
    }

    public function label(): string
    {
        return match ($this) {
            self::Recording => 'Speak',
            self::MultipleChoice => 'Choose',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Recording => '🎤',
            self::MultipleChoice => '✓',
        };
    }

    public function tagClass(): string
    {
        return match ($this) {
            self::Recording => 'question-type-tag--speak',
            self::MultipleChoice => 'question-type-tag--choose',
        };
    }
}
