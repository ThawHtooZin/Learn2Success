@extends('layouts.student')

@section('title', 'Play — '.$quiz->title)

@php
    $questionsPayload = $submission->answers->sortBy(fn ($a) => $a->question->sort_order)->values()->map(fn ($a) => [
        'answer_id' => $a->id,
        'question_id' => $a->question_id,
        'question_text' => $a->question->question_text,
        'question_type' => $a->question->question_type->value,
        'choices' => $a->question->choices(),
        'audio_url' => $a->audio_path ? asset('storage/'.$a->audio_path) : null,
        'selected_options' => $a->selected_options ?? [],
        'audio_upload_url' => route('student.submissions.audio', [$submission, $a->question]),
        'selection_url' => route('student.submissions.selection', [$submission, $a->question]),
    ]);
@endphp

@section('content')
    <div
        x-data="quizPlay({
            questions: @js($questionsPayload),
            completeUrl: @js(route('student.submissions.complete', $submission)),
            startedAt: Date.now(),
            timeLimitSeconds: @js($quiz->time_limit_seconds),
        })"
        class="pb-8"
    >
        <div class="mb-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-lg font-bold text-[#006399]">
                    <span x-text="'Question ' + (currentIndex + 1) + ' of ' + questions.length"></span>
                </div>
                <div
                    x-show="timeLimitSeconds"
                    x-cloak
                    class="quiz-timer"
                    :class="{ 'quiz-timer--urgent': remainingSeconds <= 30 }"
                >
                    <span aria-hidden="true">⏱</span>
                    <span x-text="formatTime(remainingSeconds ?? 0)"></span>
                </div>
            </div>
            <div class="mt-3 h-4 overflow-hidden rounded-full bg-[#e3f2fd]">
                <div class="h-full rounded-full bg-[#785900] transition-all" :style="'width:' + ((currentIndex + 1) / questions.length * 100) + '%'"></div>
            </div>
        </div>

        <template x-if="currentQuestion">
            <div class="student-card">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span
                        x-show="currentQuestion.question_type === 'recording'"
                        class="question-type-tag question-type-tag--speak text-base !px-3 !py-1.5"
                    >
                        <span aria-hidden="true">🎤</span> Speak — tap Record
                    </span>
                    <span
                        x-show="currentQuestion.question_type === 'multiple_choice'"
                        class="question-type-tag question-type-tag--choose text-base !px-3 !py-1.5"
                    >
                        <span aria-hidden="true">✓</span> Choose one answer
                    </span>
                </div>

                <p class="text-xl font-bold leading-snug text-[#171c1f]" x-text="currentQuestion.question_text"></p>

                <template x-if="currentQuestion.question_type === 'recording'">
                    <div class="mt-6 space-y-4">
                        <button type="button" @click="listen(currentQuestion.question_text)" class="student-btn-secondary">🎧 Listen to question</button>
                        <div class="space-y-3">
                            <button type="button" @click="startRecording()" class="flex min-h-[4rem] w-full items-center justify-center gap-2 rounded-2xl border-b-4 border-red-700 bg-red-500 text-2xl font-bold text-white shadow-lg active:translate-y-1 active:border-b-0" x-show="!recording && !countdown">
                                <span aria-hidden="true">🎤</span> Record
                            </button>
                            <button type="button" @click="stopRecording()" class="student-btn-primary !min-h-[4rem] !bg-red-600 !text-xl" x-show="recording">⏹ Stop recording</button>
                        </div>
                        <p class="text-center text-3xl font-black text-amber-500" x-show="countdown" x-text="countdown"></p>
                        <p class="text-center text-lg font-bold text-[#4f4632]" x-show="recording" x-text="'Recording: ' + recordSeconds + ' / 30 sec'"></p>
                        <audio x-show="previewUrl" :src="previewUrl" controls class="w-full rounded-xl"></audio>
                        <p class="text-base font-semibold text-red-600" x-text="error"></p>
                    </div>
                </template>

                <template x-if="currentQuestion.question_type === 'multiple_choice'">
                    <div class="mt-6 space-y-3">
                        <template x-for="(choice, idx) in currentQuestion.choices" :key="idx">
                            <label class="flex min-h-[3.5rem] cursor-pointer items-center gap-4 rounded-2xl border-2 border-[#cde5ff] bg-[#e3f2fd]/50 px-4 py-3 active:bg-[#cde5ff]">
                                <input type="radio" class="h-5 w-5" :name="'q'+currentQuestion.question_id" :value="idx" @change="saveSelection([idx])" :checked="currentQuestion.selected_options.includes(idx)">
                                <span class="text-lg font-semibold text-[#171c1f]" x-text="choice"></span>
                            </label>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" @click="prev()" :disabled="currentIndex === 0" class="student-btn-secondary opacity-40 disabled:opacity-40" :class="{ 'opacity-100': currentIndex > 0 }">Back</button>
            <button type="button" @click="next()" class="student-btn-primary" x-text="currentIndex === questions.length - 1 ? 'Finish ✓' : 'Next →'"></button>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/quiz-play.js'])
@endpush
