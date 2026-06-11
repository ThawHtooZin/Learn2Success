@extends('layouts.staff')

@section('title', 'Dashboard — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, F j, Y') }}</p>
            <h1 class="mt-1 text-2xl font-bold text-[#785900]">Welcome, {{ auth()->user()->username }}</h1>
            <p class="mt-1 text-sm text-slate-600">Your grading command center.</p>
        </div>
        @if ($kpis['ready_to_grade'] > 0)
            <a
                href="{{ route('teacher.submissions.index', ['filter' => 'ready']) }}"
                class="inline-flex min-h-11 items-center rounded-xl border-b-4 border-[#6d5100] bg-[#ffc107] px-5 py-2.5 text-sm font-bold text-[#6d5100] shadow-sm hover:bg-[#ffcd38]"
            >
                {{ $kpis['ready_to_grade'] }} ready to grade
            </a>
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card
            label="Ready to grade"
            :value="$kpis['ready_to_grade']"
            hint="Completed submissions waiting"
            accent="{{ $kpis['ready_to_grade'] > 0 ? 'gold' : 'slate' }}"
        />
        <x-dashboard.stat-card label="Graded (7 days)" :value="$kpis['graded_last_7_days']" hint="Recently finished" accent="green" />
        <x-dashboard.stat-card label="In progress" :value="$kpis['in_progress']" hint="Students still taking quizzes" accent="sky" />
        <x-dashboard.stat-card label="Active students" :value="$kpis['active_students']" hint="Submitted in last 30 days" accent="slate" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-dashboard.chart-panel
            title="Ready to grade (last 14 days)"
            chart-id="teacher-workload-chart"
            type="line"
        />
        <x-dashboard.chart-panel
            title="Submission status"
            chart-id="teacher-status-chart"
            type="doughnut"
            height="12rem"
        />
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-700">Priority queue</h2>
                <span class="text-xs text-slate-500">Oldest waiting first</span>
            </div>

            @if ($priorityQueue->isEmpty())
                <p class="mt-6 rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Nothing waiting — you're all caught up.
                </p>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($priorityQueue as $row)
                        <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $row['student'] }}</p>
                                <p class="text-sm text-slate-600">{{ $row['quiz'] }}</p>
                                <p class="mt-1 text-xs text-slate-400">Waiting {{ $row['waiting_since'] }}</p>
                            </div>
                            <a
                                href="{{ $row['grade_url'] }}"
                                class="inline-flex min-h-10 items-center rounded-lg bg-[#785900] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6d5100]"
                            >
                                Grade
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700">Quick actions</h2>
            <div class="mt-4 grid gap-2">
                <a href="{{ route('teacher.submissions.index', ['filter' => 'ready']) }}" class="flex min-h-11 items-center justify-between rounded-xl border border-[#cde5ff] bg-[#f0f8ff] px-4 py-3 text-sm font-semibold text-[#006399] hover:bg-[#e3f2fd]">
                    Full grading queue
                    <span>→</span>
                </a>
                <a href="{{ route('teacher.submissions.by-student') }}" class="flex min-h-11 items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-50">
                    By student
                    <span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('teacher.submissions.index', ['filter' => 'graded']) }}" class="flex min-h-11 items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-50">
                    Graded submissions
                    <span class="text-slate-400">→</span>
                </a>
            </div>
        </section>
    </div>

    <script type="application/json" id="staff-dashboard-data">
        @json(['charts' => $charts])
    </script>
@endsection

@push('scripts')
    @vite(['resources/js/staff-dashboard.js'])
@endpush
