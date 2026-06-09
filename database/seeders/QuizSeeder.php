<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Week;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /** @var array<int, array{title: string, description: string, total_marks: int, mark_per_question: float, time_limit_seconds: int}> */
    private array $slots = [
        1 => [
            'title' => 'Quick Choose',
            'description' => 'Warm up with multiple choice — auto-graded.',
            'total_marks' => 6,
            'mark_per_question' => 2,
            'time_limit_seconds' => 180,
        ],
        2 => [
            'title' => 'Grammar Choose',
            'description' => 'Pick the best answer — auto-graded.',
            'total_marks' => 6,
            'mark_per_question' => 2,
            'time_limit_seconds' => 300,
        ],
        3 => [
            'title' => 'Speak Up',
            'description' => 'Record your voice — two speaking questions.',
            'total_marks' => 10,
            'mark_per_question' => 5,
            'time_limit_seconds' => 420,
        ],
        4 => [
            'title' => 'Choose & Speak',
            'description' => 'Multiple choice plus record your voice.',
            'total_marks' => 10,
            'mark_per_question' => 2,
            'time_limit_seconds' => 600,
        ],
    ];

    public function run(): void
    {
        Quiz::query()->updateOrCreate(
            ['title' => 'Speaking Basics'],
            [
                'description' => 'Practice short spoken answers for everyday phrases.',
                'is_active' => true,
                'total_marks' => 10,
                'mark_per_question' => 5,
                'week_id' => null,
                'time_limit_seconds' => null,
                'sort_order_in_week' => 0,
            ],
        );

        Quiz::query()->updateOrCreate(
            ['title' => 'Grammar MC Drill'],
            [
                'description' => 'Multiple choice grammar check — auto-graded.',
                'is_active' => true,
                'total_marks' => 6,
                'mark_per_question' => 2,
                'week_id' => null,
                'time_limit_seconds' => 300,
                'sort_order_in_week' => 0,
            ],
        );

        $weeks = Week::query()->orderBy('week_number')->get();

        foreach ($weeks as $week) {
            for ($slot = 0; $slot < 4; $slot++) {
                $config = $this->slots[$slot + 1];

                Quiz::query()->updateOrCreate(
                    [
                        'week_id' => $week->id,
                        'sort_order_in_week' => $slot,
                    ],
                    [
                        'title' => "Week {$week->week_number} · {$config['title']}",
                        'description' => $config['description'],
                        'is_active' => true,
                        'total_marks' => $config['total_marks'],
                        'mark_per_question' => $config['mark_per_question'],
                        'time_limit_seconds' => $config['time_limit_seconds'],
                    ],
                );
            }
        }
    }
}
