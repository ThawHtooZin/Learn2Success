@extends('layouts.staff')

@section('title', ($canGrade ? 'Grade' : 'View').' submission')

@section('content')
    @php
        $quiz = $submission->quiz;
        $sortedAnswers = $submission->answers->sortBy(fn ($a) => $a->question->sort_order);
        $initialMarks = $sortedAnswers->mapWithKeys(fn ($a) => [
            $a->id => old('answers.'.$a->id.'.mark', $a->mark ?? 0),
        ])->all();
        $pageHeading = $canGrade ? 'Grade submission' : 'Submission details';
    @endphp

    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <x-staff-nav-trail
                :items="[
                    ['label' => $canGrade ? 'Grade' : 'Submissions', 'url' => route($indexRoute)],
                    ['label' => $submission->user->username],
                    ['label' => $quiz->title],
                ]"
                :title="$pageHeading"
            />
            <p class="mt-2 text-sm text-slate-600">
                Student: {{ $submission->user->username }} ·
                Started: {{ $submission->started_at?->format('M j, g:i A') }}
                @if ($submission->completed_at)
                    · Completed: {{ $submission->completed_at->format('M j, g:i A') }}
                    · Duration: {{ $submission->duration_seconds }}s
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if (! $canGrade)
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                    View only — teachers grade submissions
                </span>
            @endif
            <span class="rounded-full bg-[#ffdf9e] px-4 py-2 text-sm font-bold text-[#6d5100]">
                Quiz total: {{ $quiz->total_marks }} marks
            </span>
            @if (! $canGrade && $submission->total_mark !== null)
                <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-bold text-green-800">
                    Score: {{ $submission->total_mark }} / {{ $quiz->total_marks }}
                </span>
            @endif
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Quiz details (read-only)</h2>
        @if ($canGrade)
            <p class="mt-2 text-xs text-slate-500">Teachers grade submissions only — quiz content is managed by admins.</p>
        @else
            <p class="mt-2 text-xs text-slate-500">Admins can review submissions and grades but cannot change marks or feedback.</p>
        @endif

        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="font-medium text-slate-500">Total marks</dt>
                <dd class="mt-0.5 text-lg font-bold text-[#785900]">{{ $quiz->total_marks }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">Mark per question (max)</dt>
                <dd class="mt-0.5 text-lg font-bold text-[#785900]">{{ number_format($maxPerQuestion, 2) }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">Questions</dt>
                <dd class="mt-0.5 font-semibold">{{ $quiz->questions->count() }}</dd>
            </div>
            @if ($quiz->time_limit_seconds)
                <div>
                    <dt class="font-medium text-slate-500">Time limit</dt>
                    <dd class="mt-0.5 font-semibold">{{ gmdate('i:s', $quiz->time_limit_seconds) }}</dd>
                </div>
            @endif
            @if ($quiz->week)
                <div>
                    <dt class="font-medium text-slate-500">Week</dt>
                    <dd class="mt-0.5 font-semibold">Week {{ $quiz->week->week_number }} — {{ $quiz->week->title }}</dd>
                </div>
            @endif
            <div>
                <dt class="font-medium text-slate-500">Question types</dt>
                <dd class="mt-1">
                    <x-question-type-tags :types="$quiz->distinctQuestionTypes()" />
                </dd>
            </div>
        </dl>

        @if ($quiz->description)
            <p class="mt-4 text-sm text-slate-700">{{ $quiz->description }}</p>
        @endif
    </div>

    @if ($canGrade)
        <form
            method="POST"
            action="{{ route('teacher.submissions.update', $submission) }}"
            class="space-y-6"
            x-data="teacherGradingForm({
                maxPerQuestion: @js((float) $maxPerQuestion),
                quizTotal: @js((float) $quiz->total_marks),
                marks: @js($initialMarks),
            })"
            @submit="if (!canSubmit()) { $event.preventDefault(); }"
        >
            @csrf
            @method('PUT')

            <div
                class="sticky top-16 z-10 rounded-xl border px-4 py-3 text-sm"
                :class="isOverTotal() || hasQuestionErrors() ? 'border-red-200 bg-red-50 text-red-800' : 'border-slate-200 bg-white text-slate-700'"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span>
                        Running total:
                        <strong x-text="formatMark(runningTotal)"></strong>
                        /
                        <span>{{ $quiz->total_marks }}</span>
                    </span>
                    <span x-show="!isOverTotal() && !hasQuestionErrors()" x-cloak>
                        Remaining: <strong x-text="formatMark(remainingTotal())"></strong>
                    </span>
                </div>
                <p x-show="isOverTotal()" x-cloak class="mt-1 font-medium">
                    Total exceeds the quiz maximum. Lower one or more marks before saving.
                </p>
                <p x-show="hasQuestionErrors() && !isOverTotal()" x-cloak class="mt-1 font-medium">
                    One or more questions exceed the per-question maximum of {{ number_format($maxPerQuestion, 2) }}.
                </p>
            </div>

            @foreach ($sortedAnswers as $answer)
                @include('staff.submissions._answer-card', ['answer' => $answer, 'maxPerQuestion' => $maxPerQuestion, 'readOnly' => false])
            @endforeach

            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <label class="block text-sm font-medium">Overall teacher feedback</label>
                <textarea name="teacher_feedback" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('teacher_feedback', $submission->teacher_feedback) }}</textarea>
            </div>

            @error('total_mark')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button
                type="submit"
                class="min-h-11 rounded-lg px-6 py-2.5 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50"
                :class="canSubmit() ? 'bg-slate-900 hover:bg-slate-800' : 'bg-slate-400'"
                :disabled="!canSubmit()"
            >
                Save grading
            </button>
        </form>
    @else
        <div class="space-y-6">
            @foreach ($sortedAnswers as $answer)
                @include('staff.submissions._answer-card', ['answer' => $answer, 'maxPerQuestion' => $maxPerQuestion, 'readOnly' => true])
            @endforeach

            @if ($submission->teacher_feedback)
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-slate-700">Overall teacher feedback</h3>
                    <p class="mt-2 whitespace-pre-wrap text-sm text-slate-600">{{ $submission->teacher_feedback }}</p>
                </div>
            @endif

            <a href="{{ route($indexRoute) }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Back to submissions
            </a>
        </div>
    @endif
@endsection
