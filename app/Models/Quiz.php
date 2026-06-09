<?php

namespace App\Models;

use App\Enums\QuestionType;
use Database\Factories\QuizFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['week_id', 'title', 'description', 'is_active', 'total_marks', 'mark_per_question', 'time_limit_seconds', 'sort_order_in_week'])]
class Quiz extends Model
{
    /** @use HasFactory<QuizFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'mark_per_question' => 'decimal:2',
        ];
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function effectiveMarkPerQuestion(): float
    {
        if ($this->mark_per_question && (float) $this->mark_per_question > 0) {
            return (float) $this->mark_per_question;
        }

        $count = $this->questions()->count();

        return $count > 0 ? round($this->total_marks / $count, 2) : 0;
    }

    public function isAllMultipleChoice(): bool
    {
        return $this->questions()->count() > 0
            && $this->questions()->where('question_type', '!=', 'multiple_choice')->doesntExist();
    }

    /**
     * @return array<int, QuestionType>
     */
    public function distinctQuestionTypes(): array
    {
        $types = $this->relationLoaded('questions')
            ? $this->questions->pluck('question_type')
            : $this->questions()->pluck('question_type');

        return $types
            ->unique()
            ->sortBy(fn (QuestionType $type) => match ($type) {
                QuestionType::Recording => 0,
                QuestionType::MultipleChoice => 1,
            })
            ->values()
            ->all();
    }
}
