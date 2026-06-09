@extends('layouts.staff')

@section('title', 'Quizzes')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Quizzes</h1>
        <a href="{{ route('manage.quizzes.create') }}" class="min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Create quiz</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Week</th>
                    <th class="px-4 py-3">Questions</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quizzes as $quiz)
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
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $quizzes->links() }}</div>
@endsection
