@props([
    'table' => null,
    'searchPlaceholder' => 'Search…',
    'showSearch' => true,
    'clearIgnore' => [],
])

@php
    /** @var \App\Support\Tables\TableQuery|null $tableQuery */
    $tableQuery = $table;
@endphp

<form method="GET" action="{{ url()->current() }}" class="data-table-toolbar border-b border-slate-100 bg-slate-50/80 px-4 py-3">
    <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end">
        @if ($showSearch)
            <div class="min-w-[12rem] flex-1">
                <label for="data-table-q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <input
                    id="data-table-q"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"
                >
            </div>
        @endif

        @if (isset($filters))
            <div class="flex flex-wrap items-end gap-3">
                {{ $filters }}
            </div>
        @endif

        <div class="flex flex-wrap items-end gap-2">
            @if ($tableQuery)
                <div>
                    <label for="data-table-per-page" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Per page</label>
                    <select
                        id="data-table-per-page"
                        name="per_page"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"
                        onchange="this.form.submit()"
                    >
                        @foreach (\App\Support\Tables\TableQuery::PER_PAGE_OPTIONS as $size)
                            <option value="{{ $size }}" @selected($tableQuery->perPage() === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="sort" value="{{ $tableQuery->sortColumn() }}">
                <input type="hidden" name="direction" value="{{ $tableQuery->sortDirection() }}">
            @endif

            <button type="submit" class="min-h-10 rounded-lg bg-[#785900] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6d5100]">
                Apply
            </button>

            @if ($tableQuery?->hasActiveFilters(ignoreValues: $clearIgnore))
                <a href="{{ $tableQuery->clearUrl() }}" class="min-h-10 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Clear
                </a>
            @endif
        </div>
    </div>
</form>
