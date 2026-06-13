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
        <p class="mt-2 text-xs font-semibold text-[#785900]">
            Speaking answer — {{ $readOnly ? 'graded out of' : 'grade out of' }} {{ number_format($maxPerQuestion, 2) }} marks max.
        </p>
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
            @if ($answer->mark !== null && ! $readOnly)
                · Previous mark: {{ $answer->mark }}
            @endif
        </p>
    @endif

    @if ($readOnly)
        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="font-medium text-slate-500">Mark</dt>
                <dd class="mt-0.5 font-semibold">
                    {{ $answer->mark !== null ? number_format((float) $answer->mark, 2) : '—' }}
                    <span class="font-normal text-slate-500">/ {{ number_format($maxPerQuestion, 2) }}</span>
                </dd>
            </div>
            @if ($answer->teacher_feedback)
                <div class="sm:col-span-2">
                    <dt class="font-medium text-slate-500">Question feedback</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-slate-600">{{ $answer->teacher_feedback }}</dd>
                </div>
            @endif
        </dl>
    @else
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
    @endif
</div>
