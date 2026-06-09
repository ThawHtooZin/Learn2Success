<?php

namespace App\Services\Weeks;

use App\Models\User;
use App\Models\Week;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class WeekUnlockService
{
    public function unlocksAt(User $user, Week $week): CarbonInterface
    {
        return $user->created_at
            ->copy()
            ->startOfDay()
            ->addDays($week->unlock_after_days);
    }

    public function isUnlocked(User $user, Week $week): bool
    {
        return Carbon::now()->startOfDay()->gte($this->unlocksAt($user, $week));
    }

    public function daysUntilUnlock(User $user, Week $week): int
    {
        if ($this->isUnlocked($user, $week)) {
            return 0;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($this->unlocksAt($user, $week), false);
    }

    public function assertUnlocked(User $user, Week $week): void
    {
        abort_unless($this->isUnlocked($user, $week), 403, 'This week is not unlocked yet.');
    }
}
