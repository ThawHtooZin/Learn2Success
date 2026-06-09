<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Weeks\WeekJourneyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly WeekJourneyService $journey,
    ) {}

    public function __invoke(): View
    {
        return view('student.dashboard', [
            'weeks' => $this->journey->weekMap(Auth::user()),
        ]);
    }
}
