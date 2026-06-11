@extends('layouts.staff')

@section('title', 'By student')

@section('content')
    <x-staff-nav-trail
        class="mb-4"
        :items="[
            ['label' => 'Students'],
        ]"
        title="Submissions by student"
    />

    <div class="space-y-6">
        @forelse ($students as $student)
            <section class="rounded-xl border border-slate-200 bg-white p-4">
                <h2 class="font-semibold">{{ $student->username }}</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($student->submissions as $submission)
                        <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                            <span>{{ $submission->quiz->title }} — {{ $submission->created_at->format('M j, Y') }}</span>
                            <a href="{{ route('teacher.submissions.show', $submission) }}" class="font-medium hover:underline">View</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <p class="text-sm text-slate-600">No student submissions yet.</p>
        @endforelse
    </div>
@endsection
