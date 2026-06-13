@extends('layouts.staff')

@section('title', 'Dashboard — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <p class="text-sm font-medium text-slate-500">{{ now()->format('l, F j, Y') }}</p>
        <h1 class="mt-1 text-2xl font-bold text-[#785900]">Good day, {{ auth()->user()->username }}</h1>
        <p class="mt-1 text-sm text-slate-600">Program overview at a glance.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card label="Students" :value="$kpis['students']" hint="Registered learners" accent="gold" />
        <x-dashboard.stat-card label="Teachers" :value="$kpis['teachers']" hint="Grading staff" accent="sky" />
        <x-dashboard.stat-card label="Active quizzes" :value="$kpis['active_quizzes']" hint="Visible to students" accent="green" />
        <x-dashboard.stat-card
            label="Pending grades"
            :value="$kpis['pending_grades']"
            hint="Completed, awaiting teacher"
            accent="{{ $kpis['pending_grades'] > 0 ? 'gold' : 'slate' }}"
        />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-dashboard.chart-panel
            title="Completed submissions (last 14 days)"
            chart-id="admin-submissions-chart"
            type="line"
        />
        <x-dashboard.chart-panel
            title="Grading status"
            chart-id="admin-status-chart"
            type="doughnut"
            height="12rem"
        />
    </div>

    <div class="mt-6">
        <x-dashboard.chart-panel
            title="Attempts by week"
            chart-id="admin-weeks-chart"
            type="bar"
            height="16rem"
        />
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-1">
            <h2 class="text-sm font-semibold text-slate-700">Quick actions</h2>
            <div class="mt-4 grid gap-2">
                <a href="{{ route('admin.users.create') }}" class="flex min-h-11 items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-50">
                    Create user
                    <span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('manage.quizzes.create') }}" class="flex min-h-11 items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-50">
                    Create quiz
                    <span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.weeks.create') }}" class="flex min-h-11 items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-50">
                    Create week
                    <span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.submissions.index') }}" class="flex min-h-11 items-center justify-between rounded-xl border border-[#cde5ff] bg-[#f0f8ff] px-4 py-3 text-sm font-semibold text-[#006399] hover:bg-[#e3f2fd]">
                    View submissions
                    <span>→</span>
                </a>
                <a href="{{ route('manage.quizzes.index') }}" class="flex min-h-11 items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-50">
                    Manage quizzes
                    <span class="text-slate-400">→</span>
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-700">Recent activity</h2>
                <a href="{{ route('admin.submissions.index') }}" class="text-xs font-semibold text-[#006399] hover:underline">All submissions</a>
            </div>

            @if ($recentSubmissions->isEmpty())
                <p class="mt-6 rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    No submissions yet. Students will appear here after they start quizzes.
                </p>
            @else
                <div class="mt-4 overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Student</th>
                                <th class="px-4 py-3">Quiz</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentSubmissions as $row)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3 font-medium">{{ $row['student'] }}</td>
                                    <td class="px-4 py-3">{{ $row['quiz'] }}</td>
                                    <td class="px-4 py-3">
                                        <x-dashboard.status-chip :status="$row['status']">{{ $row['status_label'] }}</x-dashboard.status-chip>
                                        <span class="mt-1 block text-xs text-slate-400">{{ $row['relative_time'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($row['show_url'])
                                            <a href="{{ $row['show_url'] }}" class="font-semibold text-[#006399] hover:underline">View</a>
                                        @else
                                            <span class="text-slate-400">In progress</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <script type="application/json" id="staff-dashboard-data">
        @json(['charts' => $charts])
    </script>
@endsection

@push('scripts')
    @vite(['resources/js/staff-dashboard.js'])
@endpush
