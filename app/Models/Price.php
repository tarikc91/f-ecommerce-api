<?php

namespace App\Models;

class Price
{
    public function __construct(private float $value) {}

    /**
     * Static constructor
     *
     * @param float $value
     * @return void
     */
    public static function of(float $value): static
    {
        return new static($value);
    }

    /**
     * Get the underlying float value
     *
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Add the tax to an amount
     *
     * @param float $amount
     * @return float
     */
    public function addTax(): static
    {
        return new static($this->value * config('shop.tax_rate'));
    }

    /**
     * Calculate the discount amount
     *
     * @param integer|null $percentage
     * @return static
     */
    public function discountAmount(?int $percentage = null): static
    {
        $percentage = $percentage ?? config('shop.discount_threshold_percentage');

        return new static($this->value * ($percentage / 100));
    }

    /**
     * Calculate the discounted price
     *
     * @param float $price
     * @param integer $percentage
     * @return float
     */
    public function addDiscount(int $percentage): static
    {
        $discountAmount = $this->discountAmount($percentage)->value();

        return new static($this->value - $discountAmount);
    }

    /**
     * Subtract from the price value
     *
     * @param float $value
     * @return static
     */
    public function subtract(float $value): static
    {
        return new static($this->value - $value);
    }

    /**
     * Checks if price is over discout threshold
     *
     * @param float $value
     * @return static
     */
    public function hasDiscount(float $value): bool
    {
        return $value > config('shop.discount_threshold_price') ? true : false;
    }
}
