<?php

namespace App\UrlQueryFilters;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface Filter
{
    /**
     * Handles the filtering
     *
     * @param Builder $query
     * @param string|null $filterValue
     * @return Builder
     */
    public static function handle(Builder $query, ?string $filterValue): Builder;
}
