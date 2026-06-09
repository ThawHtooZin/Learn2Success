<?php

namespace Database\Seeders;

use App\Models\Week;
use Illuminate\Database\Seeder;

class WeekSeeder extends Seeder
{
    public function run(): void
    {
        $weeks = [
            [
                'title' => 'Week 1 — Foundations',
                'week_number' => 1,
                'unlock_after_days' => 0,
                'description' => 'Start your journey with the basics.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Week 2 — Building Up',
                'week_number' => 2,
                'unlock_after_days' => 7,
                'description' => 'Unlocks 7 days after you join.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Week 3 — Going Further',
                'week_number' => 3,
                'unlock_after_days' => 14,
                'description' => 'Unlocks 14 days after you join.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Week 4 — Level Up',
                'week_number' => 4,
                'unlock_after_days' => 21,
                'description' => 'Unlocks 21 days after you join.',
                'sort_order' => 4,
            ],
        ];

        foreach ($weeks as $week) {
            Week::query()->updateOrCreate(
                ['week_number' => $week['week_number']],
                array_merge($week, ['is_active' => true]),
            );
        }
    }
}
