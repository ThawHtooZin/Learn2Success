<?php

namespace App\Models;

use App\Enums\QuestionType;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['quiz_id', 'question_text', 'question_type', 'meta', 'sort_order'])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'question_type' => QuestionType::class,
            'meta' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * @return list<string>
     */
    public function choices(): array
    {
        return $this->meta['choices'] ?? [];
    }

    /**
     * @return list<int>
     */
    public function correctOptionIndexes(): array
    {
        return $this->meta['correct_option_indexes'] ?? [];
    }
}
