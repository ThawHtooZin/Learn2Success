@props([
    'column',
    'label',
    'table' => null,
    'align' => 'left',
])

@php
    /** @var \App\Support\Tables\TableQuery|null $tableQuery */
    $tableQuery = $table;
    $alignClass = match ($align) {
        'right' => 'text-right',
        'center' => 'text-center',
        default => 'text-left',
    };
@endphp

<th {{ $attributes->merge(['class' => "px-4 py-3 font-medium {$alignClass}"]) }}>
    @if ($tableQuery && $tableQuery->isSortable($column))
        <a
            href="{{ $tableQuery->sortUrl($column) }}"
            class="inline-flex items-center gap-1 font-medium text-slate-700 hover:text-[#785900] {{ $align === 'right' ? 'float-right' : '' }}"
        >
            <span>{{ $label }}</span>
            <span class="text-xs text-slate-400" aria-hidden="true">{{ $tableQuery->sortIndicator($column) }}</span>
        </a>
    @else
        {{ $label }}
    @endif
</th>
