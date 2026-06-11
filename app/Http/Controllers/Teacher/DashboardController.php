<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\StaffDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StaffDashboardService $dashboard,
    ) {}

    public function __invoke(): View
    {
        $overview = $this->dashboard->teacherOverview();

        return view('teacher.dashboard', [
            'kpis' => $overview['kpis'],
            'charts' => $overview['charts'],
            'priorityQueue' => $overview['priority_queue'],
        ]);
    }
}
