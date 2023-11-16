<?php

namespace App\UrlQueryFilters\Filters;

use App\UrlQueryFilters\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductPriceFilter implements Filter
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
        if(!self::isRange($filterValue)) {
            return $query->where('products.price', self::normalize($filterValue));
        }

        list($min, $max) = self::getRangeValues($filterValue);

        if(is_null($min) && $max) {
            return $query->where('products.price', '<=', $max);
        }

        if($min && is_null($max)) {
            return $query->where('products.price', '>=', $min);
        }

        if($min && $max) {
            return $query->whereBetween('products.price', [$min, $max]);
        }

        return $query;
    }

    /**
     * Checks if the value is a range
     *
     * @param string $value
     * @return boolean
     */
    public static function isRange(string $value): bool
    {
        return str_contains('|', $value);
    }

    /**
     * Creates a range
     *
     * @param string $value
     * @return array
     */
    public static function getRangeValues(string $value): array
    {
        $values = explode('|', $value);

        return [
            self::normalize($values[0]),
            self::normalize($values[1])
        ];
    }

    /**
     * Normalizes the value to be an positive integer or null
     *
     * @param string $value
     * @return integer|null
     */
    public static function normalize(string $value): ?int
    {
        return is_numeric($value) ? abs((int)$value) : null;
    }
}
