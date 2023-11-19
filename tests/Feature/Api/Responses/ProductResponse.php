<?php

namespace Tests\Feature\Api\Responses;

class ProductResponse
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
            'name' => 'string',
            'description' => 'string',
            'sku' => 'string',
            'published' => 'boolean',
            'price_ex_tax' => 'double',
            'price_inc_tax' => 'double',
            'price_list_price_ex_tax' => 'double|null',
            'price_list_price_inc_tax' => 'double|null',
            'contract_price_ex_tax' => 'double|null',
            'contract_price_inc_tax' => 'double|null',
            'final_price_ex_tax' => 'double',
            'final_price_inc_tax' => 'double',
            'currency' => 'string'
        ];
    }
}
