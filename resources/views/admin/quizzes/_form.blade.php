@php
    $defaultQuestion = [
        'question_text' => '',
        'question_type' => 'recording',
        'choices' => ['', ''],
        'correct_option_indexes' => [],
    ];

    $initialQuestions = old('questions', isset($quiz) ? $quiz->questions->map(fn ($q) => [
        'question_text' => $q->question_text,
        'question_type' => $q->question_type->value,
        'choices' => $q->choices() ?: ['', ''],
        'correct_option_indexes' => $q->correctOptionIndexes(),
    ])->values()->all() : [$defaultQuestion]);

    $initialQuestions = collect($initialQuestions)->map(fn ($q) => array_merge($defaultQuestion, $q))->values()->all();
@endphp

<div x-data="quizForm({ questions: @js($initialQuestions) })">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Title</label>
            <input name="title" value="{{ old('title', $quiz->title ?? '') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $quiz->description ?? '') }}</textarea>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium">Total marks</label>
                <input type="number" name="total_marks" value="{{ old('total_marks', $quiz->total_marks ?? 10) }}" min="1" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Mark per question (optional)</label>
                <input type="number" step="0.01" name="mark_per_question" value="{{ old('mark_per_question', $quiz->mark_per_question ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Time limit (seconds)</label>
            <input type="number" name="time_limit_seconds" value="{{ old('time_limit_seconds', $quiz->time_limit_seconds ?? '') }}" min="1" placeholder="Optional — no limit" class="mt-1 w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">Optional countdown during play. Assign week and order from <strong>Weeks → Edit week</strong>.</p>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $quiz->is_active ?? true))>
            Active (visible to students)
        </label>
    </div>

    <div class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold">Questions</h2>
            <button type="button" @click="addQuestion()" class="text-sm font-medium text-slate-900 hover:underline">+ Add question</button>
        </div>

        <template x-for="(question, qi) in questions" :key="qi">
            <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-2 flex justify-between">
                    <span class="text-sm font-medium" x-text="'Question ' + (qi + 1)"></span>
                    <button type="button" @click="removeQuestion(qi)" class="text-sm text-red-600" x-show="questions.length > 1">Remove</button>
                </div>
                <input type="hidden" :name="'questions['+qi+'][question_text]'" x-model="question.question_text">
                <textarea x-model="question.question_text" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Question text" required></textarea>

                <select
                    :name="'questions['+qi+'][question_type]'"
                    x-model="question.question_type"
                    @change="onTypeChange(qi)"
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                >
                    <option value="recording">Recording (Speak)</option>
                    <option value="multiple_choice">Multiple choice (Choose)</option>
                </select>

                <div class="mt-3" x-show="question.question_type !== 'recording'">
                    <template x-for="(choice, ci) in question.choices" :key="ci">
                        <div class="mt-2 flex items-center gap-2">
                            <input type="text" :name="'questions['+qi+'][choices]['+ci+']'" x-model="question.choices[ci]" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Choice">
                            <label class="flex items-center gap-1 text-xs whitespace-nowrap">
                                <input type="checkbox" :checked="isCorrect(qi, ci)" @change="toggleCorrect(qi, ci)">
                                Correct
                            </label>
                        </div>
                    </template>
                    <button type="button" @click="addChoice(qi)" class="mt-2 text-xs font-medium hover:underline">+ Add choice</button>
                    <template x-for="idx in (question.correct_option_indexes || [])" :key="'correct-'+idx">
                        <input type="hidden" :name="'questions['+qi+'][correct_option_indexes][]'" :value="idx">
                    </template>
                </div>
            </div>
        </template>
    </div>

    <button type="submit" class="mt-6 min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Save quiz</button>
</div>

@push('scripts')
    @vite(['resources/js/quiz-form.js'])
@endpush
