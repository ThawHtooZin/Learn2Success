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

<div data-quiz-form>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Title</label>
            <input name="title" value="{{ old('title', $quiz->title ?? '') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $quiz->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium">Total marks</label>
                <input type="number" name="total_marks" value="{{ old('total_marks', $quiz->total_marks ?? 10) }}" min="1" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @error('total_marks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Mark per question (optional)</label>
                <input type="number" step="0.01" name="mark_per_question" value="{{ old('mark_per_question', $quiz->mark_per_question ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @error('mark_per_question')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Time limit (seconds)</label>
            <input type="number" name="time_limit_seconds" value="{{ old('time_limit_seconds', $quiz->time_limit_seconds ?? '') }}" min="1" placeholder="Optional — no limit" class="mt-1 w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">Optional countdown during play. Assign week and order from <strong>Weeks → Edit week</strong>.</p>
            @error('time_limit_seconds')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $quiz->is_active ?? true))>
            Active (visible to students)
        </label>
    </div>

    <div class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold">Questions</h2>
            <button type="button" data-add-question class="text-sm font-medium text-slate-900 hover:underline">+ Add question</button>
        </div>

        @error('questions')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror

        <div data-questions-list>
            @foreach ($initialQuestions as $qi => $question)
                @include('admin.quizzes._question', [
                    'qi' => $qi,
                    'question' => $question,
                    'hideRemove' => count($initialQuestions) <= 1,
                ])
            @endforeach
        </div>

        <template data-question-template>
            @include('admin.quizzes._question', ['qi' => '__INDEX__', 'question' => $defaultQuestion])
        </template>
    </div>

    <button type="submit" class="mt-6 min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Save quiz</button>
</div>
