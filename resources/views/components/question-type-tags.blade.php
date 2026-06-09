@props(['types' => [], 'class' => ''])

@if (count($types) > 0)
    <div {{ $attributes->merge(['class' => 'flex flex-wrap gap-1.5 '.$class]) }}>
        @foreach ($types as $type)
            <span class="question-type-tag {{ $type->tagClass() }}">
                <span aria-hidden="true">{{ $type->icon() }}</span>
                {{ $type->label() }}
            </span>
        @endforeach
    </div>
@endif
