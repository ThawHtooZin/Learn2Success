<?php

namespace Database\Factories;

use App\Models\Week;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Week>
 */
class WeekFactory extends Factory
{
    protected $model = Week::class;

    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(1, 12);

        return [
            'title' => "Week {$number}",
            'week_number' => $number,
            'unlock_after_days' => ($number - 1) * 7,
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => $number,
        ];
    }
}
