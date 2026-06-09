<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Week;
use App\Services\Weeks\WeekJourneyService;
use App\Services\Weeks\WeekUnlockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WeekController extends Controller
{
    public function __construct(
        private readonly WeekUnlockService $unlock,
        private readonly WeekJourneyService $journey,
    ) {}

    public function show(Week $week): View
    {
        abort_unless($week->is_active, 404);

        $student = Auth::user();
        $this->unlock->assertUnlocked($student, $week);

        $week->loadCount(['quizzes' => fn ($q) => $q->active()]);

        return view('student.weeks.show', [
            'week' => $week,
            'steps' => $this->journey->quizSteps($student, $week),
        ]);
    }
}
