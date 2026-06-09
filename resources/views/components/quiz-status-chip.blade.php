@props(['status'])

@php
    $classes = match ($status) {
        'In progress' => 'bg-amber-100 text-amber-900 border-amber-200',
        'Pending' => 'bg-sky-100 text-sky-900 border-sky-200',
        'Graded' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 rounded-full border-2 px-3 py-1 text-sm font-bold $classes"]) }}>
    {{ $status }}
</span>
