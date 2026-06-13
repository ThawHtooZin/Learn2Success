@extends('layouts.staff')

@section('title', 'Quizzes')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Quizzes</h1>
        <a href="{{ route('manage.quizzes.create') }}" class="min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Create quiz</a>
    </div>

    <x-data-table>
        <x-slot:toolbar>
            <x-data-table.toolbar :table="$tableQuery" search-placeholder="Search quiz title…">
                <x-slot:filters>
                    <x-data-table.filter-select
                        label="Active"
                        name="active"
                        :value="request('active')"
                        :options="['' => 'All', '1' => 'Active', '0' => 'Inactive']"
                    />
                    <x-data-table.filter-select
                        label="Week"
                        name="week"
                        :value="request('week')"
                        :options="['' => 'All', 'assigned' => 'In a week', 'unassigned' => 'Unassigned']"
                    />
                </x-slot:filters>
            </x-data-table.toolbar>
        </x-slot:toolbar>

        <x-slot:head>
            <tr>
                <x-data-table.sort-header column="title" label="Title" :table="$tableQuery" />
                <x-data-table.sort-header column="week_number" label="Week" :table="$tableQuery" />
                <x-data-table.sort-header column="questions_count" label="Questions" :table="$tableQuery" />
                <x-data-table.sort-header column="is_active" label="Active" :table="$tableQuery" />
                <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
        </x-slot:head>

        @forelse ($quizzes as $quiz)
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-medium">{{ $quiz->title }}</td>
                <td class="px-4 py-3">
                    @if ($quiz->week)
                        Week {{ $quiz->week->week_number }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3">{{ $quiz->questions_count }}</td>
                <td class="px-4 py-3">{{ $quiz->is_active ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('manage.quizzes.edit', $quiz) }}" class="font-medium hover:underline">Edit</a>
                    <form method="POST" action="{{ route('manage.quizzes.destroy', $quiz) }}" class="inline" onsubmit="return confirm('Delete quiz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="font-medium text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No quizzes match your search or filters.</td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-data-table.footer :paginator="$quizzes" />
        </x-slot:footer>
    </x-data-table>
@endsection
