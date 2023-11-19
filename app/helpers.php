<?php

use App\Models\Price;

/**
 * Returns instance of price object
 *
 * @param float $value
 * @return Price
 */
function price(float $value): Price
{
    return new Price($value);
}

function addTax(float $value): float
{
    return price($value)->addTax()->value();
}

function taxDiff(float $value): float
{
    return price($value)->addTax()->subtract($value)->value();
}

function discountAmount(float $value): float
{
    return price($value)->discountAmount()->value();
}

function hasDiscount(float $value): bool
{
    return price($value)->hasDiscount($value);
}
