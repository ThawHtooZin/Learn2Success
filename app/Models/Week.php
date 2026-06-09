<?php

namespace App\Models;

use Database\Factories\WeekFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'week_number', 'unlock_after_days', 'description', 'is_active', 'sort_order'])]
class Week extends Model
{
    /** @use HasFactory<WeekFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('sort_order_in_week');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
