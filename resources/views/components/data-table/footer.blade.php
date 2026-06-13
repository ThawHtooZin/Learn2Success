@props(['paginator'])

@if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages() || ($paginator->total() ?? 0) > 0)
    <div class="data-table-footer flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 bg-slate-50 px-4 py-3 text-sm">
        <p class="text-slate-600">
            @if ($paginator->total() > 0)
                Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
            @else
                No results
            @endif
        </p>
        @if ($paginator->hasPages())
            {{ $paginator->links('vendor.pagination.data-table') }}
        @endif
    </div>
@endif
