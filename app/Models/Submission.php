<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'quiz_id', 'status', 'total_mark', 'teacher_feedback',
    'started_at', 'completed_at', 'duration_seconds',
])]
class Submission extends Model
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
            'total_mark' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isInProgress(): bool
    {
        return $this->completed_at === null;
    }

    public function isAwaitingGrading(): bool
    {
        return $this->status === SubmissionStatus::Pending && $this->completed_at !== null;
    }

    public function isGraded(): bool
    {
        return $this->status === SubmissionStatus::Graded;
    }
}
