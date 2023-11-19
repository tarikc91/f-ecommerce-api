<?php

namespace App\UrlQueryFilters\Filters;

use App\UrlQueryFilters\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductNameFilter implements Filter
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
        // I'm not doing a wildcard for the begging of the string because
        // I want to utilize the db index to make the query faster by sacrificing some results
        return !empty($filterValue) ?
            $query->where('products.name', 'LIKE', "{$filterValue}%") : $query;
    }
}
