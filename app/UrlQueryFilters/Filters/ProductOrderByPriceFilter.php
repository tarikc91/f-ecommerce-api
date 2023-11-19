<?php

namespace App\UrlQueryFilters\Filters;

use App\UrlQueryFilters\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductOrderByPriceFilter implements Filter
{
    /**
     * Handles the filtering
     *
     * @param Builder $query
     * @param string|null $filterValue
     * @return Builder
     */
    public static function handle(Builder $query, ?string $direction): Builder
    {
        return in_array($direction, ['asc', 'desc']) ?
            $query->orderBy('final_price', $direction) :
            $query->orderBy('final_price');
    }
}
