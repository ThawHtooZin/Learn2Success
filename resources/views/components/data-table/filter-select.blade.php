@props(['label', 'name', 'value' => null, 'options' => []])

<div>
    <label for="filter-{{ $name }}" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</label>
    <select
        id="filter-{{ $name }}"
        name="{{ $name }}"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"
        onchange="this.form.submit()"
    >
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) ($value ?? request($name)) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
