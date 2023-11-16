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
            'price' => 'integer',
            'sku' => 'string',
            'published' => 'boolean'
        ];
    }
}
