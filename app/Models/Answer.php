<?php

namespace App\Models;

use Database\Factories\AnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'submission_id', 'question_id', 'audio_path', 'selected_options',
    'mark', 'teacher_feedback', 'is_auto_correct',
])]
class Answer extends Model
{
    /** @use HasFactory<AnswerFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'selected_options' => 'array',
            'mark' => 'decimal:2',
            'is_auto_correct' => 'boolean',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function isAnswered(): bool
    {
        $type = $this->question->question_type;

        return match ($type) {
            \App\Enums\QuestionType::Recording => filled($this->audio_path),
            default => is_array($this->selected_options) && count($this->selected_options) > 0,
        };
    }
}
