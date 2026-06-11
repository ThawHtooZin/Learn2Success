@props([
    'title',
    'chartId',
    'type' => 'line',
    'height' => '14rem',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm']) }}>
    <h2 class="text-sm font-semibold text-slate-700">{{ $title }}</h2>
    <div class="mt-4" style="height: {{ $height }}">
        <canvas
            id="{{ $chartId }}"
            data-chart-id="{{ $chartId }}"
            data-chart-type="{{ $type }}"
            role="img"
            aria-label="{{ $title }} chart"
        ></canvas>
    </div>
</div>
