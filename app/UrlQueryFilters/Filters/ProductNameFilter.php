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
        // name=some+name
        return empty($filterValue) ?
            $query :
            $query->where('products.name', 'LIKE', "{$filterValue}%"); 
            // I'm not doing a wildcard for the begging of the string because 
            // I want to utilize the db index to make the query faster by sacrificing some results
    }
}
