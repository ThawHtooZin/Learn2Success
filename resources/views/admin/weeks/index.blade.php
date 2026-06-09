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

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Week #</th>
                    <th class="px-4 py-3 font-medium">Unlock (days)</th>
                    <th class="px-4 py-3 font-medium">Quizzes</th>
                    <th class="px-4 py-3 font-medium">Active</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
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
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No weeks yet. Create one to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $weeks->links() }}</div>
@endsection
