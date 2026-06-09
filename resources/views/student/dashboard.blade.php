@extends('layouts.student')

@section('title', 'Your journey')

@section('content')
    <div class="journey-header mb-8">
        <p class="text-lg font-semibold text-[#006399]">Your path</p>
        <h1 class="text-3xl font-extrabold leading-tight text-[#785900]">Week by week</h1>
        <p class="mt-2 text-base font-medium text-[#4f4632]">Complete each week to unlock the next adventure.</p>
    </div>

    <div class="journey-path relative mx-auto max-w-sm pb-8">
        @foreach ($weeks as $index => $node)
            @if ($index > 0)
                <div
                    @class([
                        'journey-connector',
                        'journey-connector--active' => $node['unlocked'],
                        'journey-connector--locked' => ! $node['unlocked'],
                        'journey-connector--left' => $index % 2 === 1,
                        'journey-connector--right' => $index % 2 === 0,
                    ])
                    aria-hidden="true"
                ></div>
            @endif

            <x-journey-week-node
                :week="$node['week']"
                :unlocked="$node['unlocked']"
                :completed="$node['completed']"
                :progress-percent="$node['progress_percent']"
                :days-until="$node['days_until']"
                :unlocks-at="$node['unlocks_at']"
                :quiz-count="$node['quiz_count']"
                :align="$index % 2 === 0 ? 'left' : 'right'"
            />
        @endforeach
    </div>
@endsection
