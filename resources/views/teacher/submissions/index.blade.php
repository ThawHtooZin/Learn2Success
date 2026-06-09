@extends('layouts.staff')

@section('title', 'Submissions')

@section('content')
    <h1 class="mb-4 text-xl font-semibold">Submissions</h1>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach (['ready' => 'Ready to grade', 'in_progress' => 'In progress', 'graded' => 'Graded', 'all' => 'All'] as $key => $label)
            <a href="{{ route('teacher.submissions.index', ['filter' => $key]) }}" class="rounded-full px-3 py-1.5 {{ $filter === $key ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">Quiz</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
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
                        <td class="px-4 py-3 text-right">
                            @if (! $submission->isInProgress())
                                <a href="{{ route('teacher.submissions.show', $submission) }}" class="font-medium hover:underline">Grade</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">No submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $submissions->links() }}</div>
@endsection
