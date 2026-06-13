@extends('layouts.staff')

@section('title', 'Weeks — '.config('app.name'))

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Weeks</h1>
        <a href="{{ route('admin.weeks.create') }}" class="min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">Create week</a>
    </div>

    <p class="mb-4 text-sm text-slate-600">
        Weeks control the student journey map. Open a week to add quizzes and drag to reorder.
    </p>

    <x-data-table>
        <x-slot:toolbar>
            <x-data-table.toolbar :table="$tableQuery" search-placeholder="Search week title…">
                <x-slot:filters>
                    <x-data-table.filter-select
                        label="Active"
                        name="active"
                        :value="request('active')"
                        :options="['' => 'All', '1' => 'Active', '0' => 'Inactive']"
                    />
                </x-slot:filters>
            </x-data-table.toolbar>
        </x-slot:toolbar>

        <x-slot:head>
            <tr>
                <x-data-table.sort-header column="title" label="Title" :table="$tableQuery" />
                <x-data-table.sort-header column="week_number" label="Week #" :table="$tableQuery" />
                <x-data-table.sort-header column="unlock_after_days" label="Unlock (days)" :table="$tableQuery" />
                <x-data-table.sort-header column="quizzes_count" label="Quizzes" :table="$tableQuery" />
                <x-data-table.sort-header column="is_active" label="Active" :table="$tableQuery" />
                <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
        </x-slot:head>

        @forelse ($weeks as $week)
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-medium">{{ $week->title }}</td>
                <td class="px-4 py-3">{{ $week->week_number }}</td>
                <td class="px-4 py-3">{{ $week->unlock_after_days }}</td>
                <td class="px-4 py-3">{{ $week->quizzes_count }}</td>
                <td class="px-4 py-3">{{ $week->is_active ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.weeks.edit', $week) }}" class="font-medium hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.weeks.destroy', $week) }}" class="inline" onsubmit="return confirm('Delete this week? Linked quizzes will be unassigned.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="font-medium text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No weeks match your search or filters.</td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-data-table.footer :paginator="$weeks" />
        </x-slot:footer>
    </x-data-table>
@endsection
