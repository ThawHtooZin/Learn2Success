@extends('layouts.staff')

@section('title', 'Grade submission')

@section('content')
    @php
        $quiz = $submission->quiz;
        $sortedAnswers = $submission->answers->sortBy(fn ($a) => $a->question->sort_order);
        $initialMarks = $sortedAnswers->mapWithKeys(fn ($a) => [
            $a->id => old('answers.'.$a->id.'.mark', $a->mark ?? 0),
        ])->all();
    @endphp

    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <x-staff-nav-trail
                :items="[
                    ['label' => 'Grade', 'url' => route('teacher.submissions.index')],
                    ['label' => $submission->user->username],
                    ['label' => $quiz->title],
                ]"
                title="Grade submission"
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
        <span class="rounded-full bg-[#ffdf9e] px-4 py-2 text-sm font-bold text-[#6d5100]">
            Quiz total: {{ $quiz->total_marks }} marks
        </span>
    </div>

    <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Quiz details (read-only)</h2>
        <p class="mt-2 text-xs text-slate-500">Teachers grade submissions only — quiz content is managed by admins.</p>

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
            @php
                $question = $answer->question;
                $isRecording = $question->question_type === \App\Enums\QuestionType::Recording;
            @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <p class="font-medium">{{ $question->question_text }}</p>
                    <span class="question-type-tag {{ $question->question_type->tagClass() }}">
                        <span aria-hidden="true">{{ $question->question_type->icon() }}</span>
                        {{ $question->question_type->label() }}
                    </span>
                </div>

                @if ($isRecording)
                    <p class="mt-2 text-xs font-semibold text-[#785900]">Speaking answer — grade out of {{ number_format($maxPerQuestion, 2) }} marks max.</p>
                @endif

                @if ($answer->audio_path)
                    <audio controls class="mt-3 w-full" src="{{ asset('storage/'.$answer->audio_path) }}"></audio>
                @elseif ($isRecording)
                    <p class="mt-2 text-sm text-amber-700">No recording uploaded for this question.</p>
                @endif

                @if ($question->question_type === \App\Enums\QuestionType::MultipleChoice)
                    <div class="mt-3 space-y-1 text-sm text-slate-600">
                        @foreach ($question->choices() as $index => $choice)
                            @php
                                $selected = in_array($index, $answer->selected_options ?? [], true);
                                $correct = in_array($index, $question->correctOptionIndexes(), true);
                            @endphp
                            <p @class([
                                'rounded px-2 py-1',
                                'bg-green-50 font-medium text-green-800' => $selected && $correct,
                                'bg-red-50 font-medium text-red-800' => $selected && ! $correct,
                                'text-slate-500' => ! $selected,
                            ])>
                                {{ $choice }}
                                @if ($selected) (student choice) @endif
                                @if ($correct) ✓ correct @endif
                            </p>
                        @endforeach
                    </div>
                @elseif ($answer->selected_options)
                    <p class="mt-2 text-sm text-slate-600">Selected: {{ implode(', ', $answer->selected_options) }}</p>
                @endif

                @if ($answer->is_auto_correct !== null)
                    <p class="mt-2 text-xs text-slate-500">
                        Auto-grade: {{ $answer->is_auto_correct ? 'Correct' : 'Incorrect' }}
                        @if ($answer->mark !== null)
                            · Previous mark: {{ $answer->mark }}
                        @endif
                    </p>
                @endif

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">
                            Mark
                            <span class="font-normal text-slate-500">(max {{ number_format($maxPerQuestion, 2) }})</span>
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            :max="maxPerQuestion"
                            name="answers[{{ $answer->id }}][mark]"
                            value="{{ old('answers.'.$answer->id.'.mark', $answer->mark ?? 0) }}"
                            @input="onMarkInput({{ $answer->id }}, $event)"
                            @class([
                                'mt-1 w-full rounded-lg border px-3 py-2 text-sm',
                                'border-red-400' => $errors->has('answers.'.$answer->id.'.mark'),
                                'border-slate-300' => ! $errors->has('answers.'.$answer->id.'.mark'),
                            ])
                        >
                        <p
                            x-show="questionMarkError({{ $answer->id }})"
                            x-cloak
                            x-text="questionMarkError({{ $answer->id }})"
                            class="mt-1 text-xs text-red-600"
                        ></p>
                        @error('answers.'.$answer->id.'.mark')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium">Feedback for this question</label>
                        <textarea name="answers[{{ $answer->id }}][teacher_feedback]" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('answers.'.$answer->id.'.teacher_feedback', $answer->teacher_feedback) }}</textarea>
                    </div>
                </div>
            </div>
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
@endsection
