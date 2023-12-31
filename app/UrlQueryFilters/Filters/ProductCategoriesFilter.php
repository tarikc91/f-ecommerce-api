<?php

namespace App\UrlQueryFilters\Filters;

use App\UrlQueryFilters\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductCategoriesFilter implements Filter
{
    /**
     * Handles the filtering
     *
     * @param Builder $query
     * @param string|null $filterValue
     * @return Builder
     */
    public static function handle(Builder $query, ?string $filterValue): Builder
    {
        return !empty($filterValue) ?
            $query->whereHas(
                'categories',
                fn($query) => $query->whereIn('categories.id', explode(',', $filterValue))
            ) : $query;
    }
}
