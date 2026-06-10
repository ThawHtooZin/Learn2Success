@php
    $qi = $qi ?? 0;
    $question = array_merge([
        'question_text' => '',
        'question_type' => 'recording',
        'choices' => ['', ''],
        'correct_option_indexes' => [],
    ], $question ?? []);
    $choices = $question['choices'] ?? ['', ''];
    if (count($choices) < 2) {
        $choices = array_pad($choices, 2, '');
    }
    $correctIndexes = $question['correct_option_indexes'] ?? [];
    $correctIndex = is_array($correctIndexes) && count($correctIndexes) > 0 ? (int) $correctIndexes[0] : null;
    $isMc = ($question['question_type'] ?? 'recording') === 'multiple_choice';
@endphp

<div data-question class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
    <div class="mb-2 flex justify-between">
        <span class="text-sm font-medium" data-question-label>Question {{ is_numeric($qi) ? (int) $qi + 1 : '' }}</span>
        <button type="button" data-remove-question class="text-sm text-red-600 {{ ($hideRemove ?? false) ? 'hidden' : '' }}">Remove</button>
    </div>

    <textarea
        name="questions[{{ $qi }}][question_text]"
        rows="2"
        required
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        placeholder="Question text"
    >{{ old("questions.$qi.question_text", $question['question_text'] ?? '') }}</textarea>
    @error("questions.$qi.question_text")
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <select
        name="questions[{{ $qi }}][question_type]"
        data-question-type
        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
    >
        <option value="recording" @selected(old("questions.$qi.question_type", $question['question_type'] ?? 'recording') === 'recording')>Recording (Speak)</option>
        <option value="multiple_choice" @selected(old("questions.$qi.question_type", $question['question_type'] ?? '') === 'multiple_choice')>Multiple choice (Choose)</option>
    </select>

    <div data-mc-section class="mt-3 {{ $isMc ? '' : 'hidden' }}">
        <div data-choices-list class="space-y-2">
            @foreach ($choices as $ci => $choice)
                <div data-choice class="flex items-center gap-2">
                    <input
                        type="text"
                        name="questions[{{ $qi }}][choices][{{ $ci }}]"
                        value="{{ old("questions.$qi.choices.$ci", $choice) }}"
                        class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Choice"
                        @disabled(! $isMc)
                    >
                    <label class="flex items-center gap-1 text-xs whitespace-nowrap">
                        <input
                            type="radio"
                            name="questions[{{ $qi }}][correct_option_index]"
                            value="{{ $ci }}"
                            data-correct-option
                            @checked((string) old("questions.$qi.correct_option_index", $correctIndex) === (string) $ci)
                            @disabled(! $isMc)
                        >
                        Correct
                    </label>
                </div>
            @endforeach
        </div>
        <button type="button" data-add-choice class="mt-2 text-xs font-medium hover:underline">+ Add choice</button>
        @error("questions.$qi.choices")
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error("questions.$qi.correct_option_indexes")
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
