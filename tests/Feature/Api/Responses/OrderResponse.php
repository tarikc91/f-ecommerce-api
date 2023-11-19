<?php

namespace Tests\Feature\Api\Responses;

class OrderResponse
{
    /**
     * Gets all attributes for a product in the response with the appropriate type
     *
     * @return array
     */
    public static function getJsonAttributes(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer|null',
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'street_address' => 'string',
            'city' => 'string',
            'country' => 'string',
            'subtotal_price_ex_tax' => 'integer|double',
            'subtotal_price_inc_tax' => 'integer|double',
            'subtotal_tax' => 'integer|double',
            'tax_rate' => 'integer|double',
            'discount_percentage' => 'integer',
            'discount_amount' => 'integer|double',
            'total_price_ex_tax' => 'integer|double',
            'total_price_inc_tax' => 'integer|double',
            'total_tax' => 'integer|double',
            'orderProducts' => 'array|null'
        ];
    }
}
