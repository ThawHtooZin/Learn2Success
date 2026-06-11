@props(['items' => [], 'title' => null])

@php
    $items = collect($items)->filter(fn ($item) => filled($item['label'] ?? null))->values()->all();
@endphp

@if (count($items) > 0)
    <nav aria-label="Page path" {{ $attributes->merge(['class' => 'mb-4']) }}>
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-600">
            @foreach ($items as $index => $item)
                @if ($index > 0)
                    <li aria-hidden="true" class="text-slate-400">/</li>
                @endif
                <li>
                    @if (! empty($item['url']) && $index < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="inline-flex items-center gap-1 font-medium text-slate-600 hover:text-slate-900">
                            <span aria-hidden="true">←</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span @class(['font-semibold text-slate-900' => $index === count($items) - 1])>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif

@if ($title)
    <h1 class="text-xl font-semibold">{{ $title }}</h1>
@endif
