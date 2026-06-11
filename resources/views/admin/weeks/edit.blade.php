@extends('layouts.staff')

@section('title', 'Edit week — '.config('app.name'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <x-staff-nav-trail
                :items="[
                    ['label' => 'Weeks', 'url' => route('admin.weeks.index')],
                    ['label' => $week->title],
                ]"
                title="Edit week"
            />
            <p class="mt-1 text-sm text-slate-600">{{ $weekQuizzes->count() }} {{ str('quiz')->plural($weekQuizzes->count()) }} assigned</p>
        </div>
        <a href="{{ route('manage.quizzes.create') }}" class="min-h-11 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium hover:bg-slate-50">
            + Create new quiz
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-4">
            <form method="POST" action="{{ route('admin.weeks.update', $week) }}" class="rounded-xl border border-slate-200 bg-white p-6 xl:sticky xl:top-24">
                @csrf
                @method('PUT')
                @include('admin.weeks._form')
                <button type="submit" class="mt-6 min-h-11 w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                    Save week details
                </button>
            </form>
        </div>

        <div
            class="xl:col-span-8"
            x-data="weekQuizManager({
                quizzes: @js($weekQuizzes),
                availableQuizzes: @js($availableQuizzes),
                assignUrl: @js(route('admin.weeks.quizzes.store', $week)),
                reorderUrl: @js(route('admin.weeks.quizzes.reorder', $week)),
                removeUrlTemplate: @js(route('admin.weeks.quizzes.destroy', [$week, '__QUIZ__'])),
            })"
        >
            <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Quizzes in this week</h2>
                        <p class="mt-1 text-sm text-slate-600">Add quizzes here and drag to reorder the student path.</p>
                    </div>
                    <span
                        x-show="saving"
                        x-cloak
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"
                    >Saving order…</span>
                </div>

                <div
                    x-show="feedback"
                    x-cloak
                    x-text="feedback"
                    class="mb-4 rounded-lg px-3 py-2 text-sm"
                    :class="feedbackType === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'"
                ></div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <select
                        x-model="selectedQuizId"
                        class="min-h-11 flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm"
                    >
                        <option value="">Choose a quiz to add…</option>
                        <template x-for="quiz in availableQuizzes" :key="quiz.id">
                            <option :value="quiz.id" x-text="quiz.title"></option>
                        </template>
                    </select>
                    <button
                        type="button"
                        @click="addQuiz()"
                        :disabled="!selectedQuizId"
                        class="min-h-11 rounded-lg bg-[#785900] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#6d5100] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Add to week
                    </button>
                </div>

                <p x-show="availableQuizzes.length === 0 && quizzes.length > 0" x-cloak class="mt-2 text-xs text-slate-500">
                    All quizzes are already in this week.
                </p>

                <div class="mt-6">
                    {{-- Server-rendered fallback (visible before Alpine boots) --}}
                    @if ($weekQuizzes->isNotEmpty())
                        <ul class="space-y-2" x-show="!initialized">
                            @foreach ($weekQuizzes as $index => $quiz)
                                <li class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 sm:px-4">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-[#785900] shadow-sm">{{ $index + 1 }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium">{{ $quiz['title'] }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $quiz['questions_count'] }} questions
                                            · Time limit:
                                            @if ($quiz['time_limit_seconds'])
                                                {{ gmdate('i:s', $quiz['time_limit_seconds']) }}
                                            @else
                                                —
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ $quiz['edit_url'] }}" class="text-sm font-medium hover:underline">Edit</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500" x-show="!initialized">
                            No quizzes yet. Pick one above or create a new quiz first.
                        </div>
                    @endif

                    <div x-show="initialized" x-cloak>
                        <template x-if="quizzes.length === 0">
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                                No quizzes yet. Pick one above or create a new quiz first.
                            </div>
                        </template>

                        <ul class="space-y-2" x-show="quizzes.length > 0">
                            <template x-for="(quiz, index) in quizzes" :key="quiz.id">
                                <li
                                    draggable="true"
                                    @dragstart="dragStart(index)"
                                    @dragover="dragOver($event, index)"
                                    @dragend="dragEnd()"
                                    class="flex cursor-grab items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 active:cursor-grabbing sm:px-4"
                                    :class="{ 'opacity-60 ring-2 ring-[#006399]': dragIndex === index }"
                                >
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-[#785900] shadow-sm" x-text="index + 1"></span>

                                    <span class="hidden shrink-0 text-slate-400 sm:block" aria-hidden="true">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/></svg>
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium" x-text="quiz.title"></p>
                                        <p class="text-xs text-slate-500">
                                            <span x-text="quiz.questions_count + ' questions'"></span>
                                            · Time limit: <span x-text="formatTime(quiz.time_limit_seconds)"></span>
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-1 sm:hidden">
                                        <button type="button" @click="moveUp(index)" :disabled="index === 0" class="rounded p-1 text-slate-600 disabled:opacity-30" aria-label="Move up">↑</button>
                                        <button type="button" @click="moveDown(index)" :disabled="index === quizzes.length - 1" class="rounded p-1 text-slate-600 disabled:opacity-30" aria-label="Move down">↓</button>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2">
                                        <a :href="quiz.edit_url" class="hidden text-sm font-medium hover:underline sm:inline">Edit</a>
                                        <button
                                            type="button"
                                            @click="removeQuiz(quiz.id)"
                                            class="text-sm font-medium text-red-600 hover:underline"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </li>
                            </template>
                        </ul>

                        <p x-show="quizzes.length > 0" class="mt-3 text-xs text-slate-500">
                            Drag rows to change the order students see on the week path.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
