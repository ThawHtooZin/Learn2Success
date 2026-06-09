@props([
    'quiz',
    'step' => 1,
    'status' => 'Not started',
    'isCurrent' => false,
    'latest' => null,
    'inProgress' => null,
])

@php
    $done = $latest?->isGraded();
    $minutes = $quiz->time_limit_seconds ? (int) ceil($quiz->time_limit_seconds / 60) : null;
@endphp

<div @class([
    'journey-quiz-step',
    'journey-quiz-step--current' => $isCurrent && ! $done,
    'journey-quiz-step--done' => $done,
])>
    <div class="journey-quiz-step__rail">
        <span class="journey-quiz-step__number">{{ $done ? '✓' : $step }}</span>
    </div>

    <div class="journey-quiz-step__body">
        <h3 class="journey-quiz-step__title">{{ $quiz->title }}</h3>
        <p class="journey-quiz-step__meta">
            {{ $quiz->questions_count }} questions
            @if ($minutes)
                · {{ $minutes }} min limit
            @endif
        </p>

        <x-question-type-tags :types="$quiz->distinctQuestionTypes()" class="mt-2" />

        @if ($done)
            <p class="journey-quiz-step__score">Score: {{ $latest->total_mark }}</p>
        @endif

        <div class="journey-quiz-step__actions">
            @if ($inProgress)
                <a href="{{ route('student.quizzes.play', $quiz) }}" class="student-btn-primary !min-h-12 text-base">
                    Continue ▶
                </a>
            @else
                <a href="{{ route('student.quizzes.show', $quiz) }}" class="student-btn-primary !min-h-12 text-base">
                    {{ $done ? 'Play again 🔄' : ($isCurrent ? 'Start 🚀' : 'Open') }}
                </a>
            @endif
        </div>
    </div>
</div>
