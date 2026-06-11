<?php

namespace App\Http\Controllers\Admin;

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
        $overview = $this->dashboard->adminOverview();

        return view('admin.dashboard', [
            'kpis' => $overview['kpis'],
            'charts' => $overview['charts'],
            'recentSubmissions' => $overview['recent_submissions'],
        ]);
    }
}
