@props([
    'label',
    'value',
    'hint' => null,
    'accent' => 'gold',
])

@php
    $accents = [
        'gold' => 'border-[#ffdf9e] bg-gradient-to-br from-[#fff9eb] to-white text-[#785900]',
        'sky' => 'border-[#cde5ff] bg-gradient-to-br from-[#f0f8ff] to-white text-[#006399]',
        'green' => 'border-[#b8f5b6] bg-gradient-to-br from-[#f3fff2] to-white text-[#006e1c]',
        'slate' => 'border-slate-200 bg-gradient-to-br from-slate-50 to-white text-slate-900',
    ];
    $accentClass = $accents[$accent] ?? $accents['gold'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl border-2 p-5 shadow-sm {$accentClass}"]) }}>
    <p class="text-sm font-semibold opacity-80">{{ $label }}</p>
    <p class="mt-2 text-3xl font-bold tracking-tight">{{ $value }}</p>
    @if ($hint)
        <p class="mt-2 text-xs font-medium opacity-70">{{ $hint }}</p>
    @endif
</div>
