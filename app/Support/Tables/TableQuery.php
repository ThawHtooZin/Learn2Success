<?php

namespace App\Support\Tables;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class TableQuery
{
    /** @var list<int> */
    public const PER_PAGE_OPTIONS = [10, 15, 25, 50];

    /**
     * @param  list<string>  $sortableColumns
     */
    public function __construct(
        private readonly Request $request,
        private readonly array $sortableColumns,
        private readonly string $defaultSort = 'id',
        private readonly string $defaultDirection = 'asc',
        private readonly int $defaultPerPage = 15,
    ) {}

    /**
     * @param  list<string>  $sortableColumns
     */
    public static function make(
        Request $request,
        array $sortableColumns,
        string $defaultSort = 'id',
        string $defaultDirection = 'asc',
        int $defaultPerPage = 15,
    ): self {
        return new self($request, $sortableColumns, $defaultSort, $defaultDirection, $defaultPerPage);
    }

    public function searchTerm(): ?string
    {
        $term = trim((string) $this->request->query('q', ''));

        return $term === '' ? null : $term;
    }

    public function sortColumn(): string
    {
        $sort = (string) $this->request->query('sort', $this->defaultSort);

        return in_array($sort, $this->sortableColumns, true) ? $sort : $this->defaultSort;
    }

    public function sortDirection(): string
    {
        $direction = strtolower((string) $this->request->query('direction', $this->defaultDirection));

        return in_array($direction, ['asc', 'desc'], true) ? $direction : $this->defaultDirection;
    }

    public function perPage(): int
    {
        $perPage = (int) $this->request->query('per_page', $this->defaultPerPage);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : $this->defaultPerPage;
    }

    public function filter(string $key, ?string $default = null): ?string
    {
        $value = $this->request->query($key, $default);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @param  list<string|Closure(Builder, string): void>  $columns
     */
    public function applySearch(Builder $query, array $columns): Builder
    {
        $term = $this->searchTerm();

        if ($term === null) {
            return $query;
        }

        $query->where(function (Builder $builder) use ($columns, $term): void {
            foreach ($columns as $column) {
                if ($column instanceof Closure) {
                    $column($builder, $term);

                    continue;
                }

                if (str_contains($column, '.')) {
                    [$relation, $field] = explode('.', $column, 2);
                    $builder->orWhereHas($relation, fn (Builder $relationQuery) => $relationQuery->where($field, 'like', "%{$term}%"));

                    continue;
                }

                $builder->orWhere($column, 'like', "%{$term}%");
            }
        });

        return $query;
    }

    /**
     * @param  array<string, Closure(Builder, string): void>|null  $sortMap
     */
    public function applySort(Builder $query, ?array $sortMap = null): Builder
    {
        $column = $this->sortColumn();
        $direction = $this->sortDirection();

        if ($sortMap !== null && isset($sortMap[$column])) {
            $sortMap[$column]($query, $direction);

            return $query;
        }

        return $query->orderBy($column, $direction);
    }

    public function paginate(Builder $query): LengthAwarePaginator
    {
        return $query->paginate($this->perPage())->withQueryString();
    }

    public function sortUrl(string $column): string
    {
        $direction = 'asc';

        if ($this->sortColumn() === $column && $this->sortDirection() === 'asc') {
            $direction = 'desc';
        }

        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction,
            'page' => null,
        ]);
    }

    public function isSorted(string $column): bool
    {
        return $this->sortColumn() === $column;
    }

    public function isSortable(string $column): bool
    {
        return in_array($column, $this->sortableColumns, true);
    }

    public function sortIndicator(string $column): string
    {
        if (! $this->isSorted($column)) {
            return '↕';
        }

        return $this->sortDirection() === 'asc' ? '↑' : '↓';
    }

    /**
     * @param  list<string>  $except
     * @param  array<string, string|null>  $ignoreValues  Query keys treated as inactive when value matches (e.g. default filter).
     */
    public function hasActiveFilters(array $except = ['sort', 'direction', 'page', 'per_page'], array $ignoreValues = []): bool
    {
        return collect($this->request->query())
            ->except($except)
            ->reject(function ($value, $key) use ($ignoreValues) {
                return array_key_exists($key, $ignoreValues)
                    && (string) $value === (string) $ignoreValues[$key];
            })
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->isNotEmpty();
    }

    public function clearUrl(): string
    {
        return request()->url();
    }
}
