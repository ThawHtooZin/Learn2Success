@extends('layouts.staff')

@section('title', $pageTitle.' — '.config('app.name'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ $pageTitle }}</h1>
        @if ($canGrade)
            <a href="{{ route($byStudentRoute) }}" class="text-sm font-medium text-[#006399] hover:underline">By student</a>
        @else
            <div class="flex gap-3 text-sm">
                <a href="{{ route($byStudentRoute) }}" class="font-medium text-[#006399] hover:underline">By student</a>
                <a href="{{ route($indexRoute) }}" class="font-medium text-slate-600 hover:underline">All submissions</a>
            </div>
        @endif
    </div>

    <x-data-table>
        <x-slot:toolbar>
            <x-data-table.toolbar
                :table="$tableQuery"
                search-placeholder="Search student or quiz…"
                :clear-ignore="['filter' => 'ready']"
            >
                <x-slot:filters>
                    <x-data-table.filter-select
                        label="Status"
                        name="filter"
                        :value="$filter"
                        :options="[
                            'ready' => 'Ready to grade',
                            'in_progress' => 'In progress',
                            'graded' => 'Graded',
                            'all' => 'All',
                        ]"
                    />
                </x-slot:filters>
            </x-data-table.toolbar>
        </x-slot:toolbar>

        <x-slot:head>
            <tr>
                <x-data-table.sort-header column="student" label="Student" :table="$tableQuery" />
                <x-data-table.sort-header column="quiz" label="Quiz" :table="$tableQuery" />
                <th class="px-4 py-3 font-medium">Status</th>
                <x-data-table.sort-header column="created_at" label="Submitted" :table="$tableQuery" align="right" />
                <th class="px-4 py-3 text-right font-medium">Action</th>
            </tr>
        </x-slot:head>

        @forelse ($submissions as $submission)
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3">{{ $submission->user->username }}</td>
                <td class="px-4 py-3">{{ $submission->quiz->title }}</td>
                <td class="px-4 py-3">
                    @if ($submission->isInProgress())
                        In progress
                    @elseif ($submission->isGraded())
                        Graded
                    @else
                        Pending
                    @endif
                </td>
                <td class="px-4 py-3 text-right text-slate-500">{{ $submission->created_at?->format('M j, Y') }}</td>
                <td class="px-4 py-3 text-right">
                    @if (! $submission->isInProgress())
                        <a href="{{ route($showRoute, $submission) }}" class="font-medium hover:underline">
                            {{ $canGrade ? 'Grade' : 'View' }}
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-center text-slate-500">No submissions match your search or filters.</td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-data-table.footer :paginator="$submissions" />
        </x-slot:footer>
    </x-data-table>
@endsection
