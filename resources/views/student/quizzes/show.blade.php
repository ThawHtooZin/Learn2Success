@extends('layouts.student')

@section('title', $quiz->title)

@section('content')
    <a
        href="{{ $quiz->week_id ? route('student.weeks.show', $quiz->week_id) : route('student.dashboard') }}"
        class="inline-flex min-h-11 items-center text-lg font-bold text-[#006399]"
    >
        ← Back
    </a>

    <h1 class="mt-3 text-3xl font-extrabold leading-tight text-[#785900]">{{ $quiz->title }}</h1>
    <p class="mt-3 text-lg leading-relaxed text-[#4f4632]">{{ $quiz->description }}</p>

    <div class="mt-4 flex flex-wrap gap-2">
        <span class="rounded-full bg-[#e3f2fd] px-4 py-2 text-base font-bold text-[#006399]">{{ $quiz->questions_count }} questions</span>
        <span class="rounded-full bg-[#ffdf9e] px-4 py-2 text-base font-bold text-[#6d5100]">{{ $quiz->total_marks }} marks</span>
        @if ($quiz->time_limit_seconds)
            <span class="rounded-full bg-[#ffdf9e] px-4 py-2 text-base font-bold text-[#6d5100]">{{ (int) ceil($quiz->time_limit_seconds / 60) }} min limit</span>
        @endif
        <span class="rounded-full bg-[#e3f2fd] px-4 py-2 text-base font-bold text-[#006399]">{{ $attempts->count() }} {{ Str::plural('try', $attempts->count()) }}</span>
    </div>

    <x-question-type-tags :types="$quiz->distinctQuestionTypes()" class="mt-3" />

    <div class="mt-8 space-y-3">
        @if ($inProgress)
            <a href="{{ route('student.quizzes.play', $quiz) }}" class="student-btn-primary">
                Continue quiz ▶
            </a>
        @elseif ($attempts->isEmpty())
            <a href="{{ route('student.quizzes.play', ['quiz' => $quiz, 'start' => 1]) }}" class="student-btn-primary">
                Start quiz 🚀
            </a>
        @else
            <form method="POST" action="{{ route('student.quizzes.new-try', $quiz) }}">
                @csrf
                <button type="submit" class="student-btn-primary w-full">
                    New try 🔄
                </button>
            </form>
        @endif
    </div>

    @if ($attempts->isNotEmpty())
        <h2 class="mb-4 mt-10 text-xl font-bold text-[#785900]">Your tries</h2>
        <div class="space-y-3">
            @foreach ($attempts as $attempt)
                <div class="student-card">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-base font-semibold text-[#4f4632]">{{ $attempt->created_at->format('M j, Y') }}</span>
                        <x-quiz-status-chip :status="$attempt->isInProgress() ? 'In progress' : ($attempt->isGraded() ? 'Graded' : 'Pending')" />
                    </div>
                    @if ($attempt->isGraded())
                        <p class="mt-2 text-lg font-bold text-[#006e1c]">Score: {{ $attempt->total_mark }}</p>
                    @endif
                    @if ($attempt->duration_seconds)
                        <p class="mt-1 text-base text-[#4f4632]">Time: {{ $attempt->duration_seconds }} seconds</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
