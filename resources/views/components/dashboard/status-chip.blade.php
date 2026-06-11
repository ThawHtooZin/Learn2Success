@props(['status' => 'in_progress'])

@php
    $styles = [
        'ready' => 'bg-[#ffdf9e] text-[#6d5100]',
        'graded' => 'bg-[#d4f5d3] text-[#006419]',
        'in_progress' => 'bg-[#cde5ff] text-[#003a5d]',
    ];
    $class = $styles[$status] ?? $styles['in_progress'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {$class}"]) }}>
    {{ $slot }}
</span>
