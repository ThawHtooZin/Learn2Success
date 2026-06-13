@props(['emptyColspan' => 1, 'emptyMessage' => 'No results found.'])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200 bg-white']) }}>
    @if (isset($toolbar))
        {{ $toolbar }}
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            @if (isset($head))
                <thead class="bg-slate-50 text-left">
                    {{ $head }}
                </thead>
            @endif
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if (isset($footer))
        {{ $footer }}
    @endif
</div>
