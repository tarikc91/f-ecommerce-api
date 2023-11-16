<?php

namespace App\UrlQueryFilters;

use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Filteres the resource
     *
     * @param array $filters
     * @return Builder
     */
    public static function filter(array $filters): Builder
    {
        $query = static::query();

        foreach($filters as $key => $value) {
            $query = self::handleQueryParameter($query, $key, $value);
        }

        return $query;
    }

    /**
     * Handles individual filter
     *
     * @param Builder $query
     * @param string $filterKey
     * @param string|null $filterValue
     * @return Builder
     */
    private static function handleQueryParameter(Builder $query, string $filterKey, ?string $filterValue): Builder
    {
        list($key, $value) = self::normalize([$filterKey, $filterValue]);

        if($key === 'orderby') {
            $values = explode(',', $value);
            $key .= $values[0];
            $value = $values[1] ?? 'asc';
        }

        return array_key_exists($key, static::getUrlQueryFilters()) ?
            static::getUrlQueryFilters()[$key]::handle($query, $value) :
            $query;
    }

    /**
     * Get the urlQueryFilters array
     *
     * @return array
     *
     * @throws Exception
     */
    private static function getUrlQueryFilters(): array
    {
        if(isset(static::$urlQueryFilters)) {
            return static::$urlQueryFilters;
        }

        throw new Exception(sprintf('Static property $urlQueryFilters not defined on %s', static::class));
    }

    /**
     * Normalize the values
     *
     * @param array $values
     * @return array
     */
    private static function normalize(array $values): array
    {
        return array_map(fn($value) => strtolower($value), $values);
    }
}
