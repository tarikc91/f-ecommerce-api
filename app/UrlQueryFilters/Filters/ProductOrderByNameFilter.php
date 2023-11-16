<?php

namespace App\UrlQueryFilters\Filters;

use App\UrlQueryFilters\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductOrderByNameFilter implements Filter
{
    /**
     * Handles the filtering
     *
     * @param Builder $query
     * @param string|null $direction
     * @return Builder
     */
    public static function handle(Builder $query, ?string $direction): Builder
    {
        // orderBy=name,asc|desc
        return in_array($direction, ['asc', 'desc']) ?
            $query->orderBy('name', $direction) :
            $query->orderBy('name');
    }
}
