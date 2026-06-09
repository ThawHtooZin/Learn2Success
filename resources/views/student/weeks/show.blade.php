@extends('layouts.student')

@section('title', $week->title)

@section('content')
    <a href="{{ route('student.dashboard') }}" class="inline-flex min-h-11 items-center text-lg font-bold text-[#006399]">
        ← Journey
    </a>

    <div class="journey-header mt-3 mb-8">
        <p class="text-sm font-bold uppercase tracking-wide text-[#006399]">Week {{ $week->week_number }}</p>
        <h1 class="text-3xl font-extrabold leading-tight text-[#785900]">{{ $week->title }}</h1>
        @if ($week->description)
            <p class="mt-2 text-base font-medium text-[#4f4632]">{{ $week->description }}</p>
        @endif
    </div>

    <div class="journey-steps mx-auto max-w-md pb-6">
        <div class="journey-steps__track" aria-hidden="true"></div>

        @foreach ($steps as $step)
            <x-journey-quiz-step
                :quiz="$step['quiz']"
                :step="$step['step']"
                :status="$step['status']"
                :is-current="$step['is_current']"
                :latest="$step['latest']"
                :in-progress="$step['in_progress']"
            />
        @endforeach
    </div>
@endsection
