@props([
    'week',
    'unlocked' => false,
    'completed' => false,
    'progressPercent' => 0,
    'daysUntil' => 0,
    'unlocksAt' => null,
    'quizCount' => 0,
    'align' => 'left',
])

<div @class([
    'journey-node-wrap',
    'journey-node-wrap--left' => $align === 'left',
    'journey-node-wrap--right' => $align === 'right',
])>
    @if ($unlocked)
        <a
            href="{{ route('student.weeks.show', $week) }}"
            class="journey-week-node journey-week-node--unlocked {{ $completed ? 'journey-week-node--completed' : '' }}"
        >
            <span class="journey-week-node__badge">{{ $week->week_number }}</span>
            <span class="journey-week-node__icon">{{ $completed ? '✓' : '🎯' }}</span>
            <span class="journey-week-node__title">{{ $week->title }}</span>
            <span class="journey-week-node__meta">{{ $quizCount }} quizzes · {{ $progressPercent }}%</span>
        </a>
    @else
        <div class="journey-week-node journey-week-node--locked" aria-disabled="true">
            <span class="journey-week-node__badge">{{ $week->week_number }}</span>
            <span class="journey-week-node__icon">🔒</span>
            <span class="journey-week-node__title">{{ $week->title }}</span>
            <span class="journey-week-node__meta">
                @if ($daysUntil === 1)
                    Unlocks tomorrow
                @elseif ($daysUntil > 1)
                    {{ $daysUntil }} days left
                @else
                    Unlocks {{ $unlocksAt?->format('M j') }}
                @endif
            </span>
        </div>
    @endif
</div>
