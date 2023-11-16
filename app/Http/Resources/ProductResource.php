<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description'  => $this->description,
            'default_price_ex_tax' => $this->defaultPriceExTax(),
            'default_price_inc_tax' => $this->defaultPriceIncTax(),
            'calculated_price_ex_tax' => $this->calculatedPriceExTax(),
            'calculated_price_inc_tax' => $this->calculatedPriceIncTax(),
            'price_ex_tax' => $this->priceExTax(),
            'price_inc_tax' => $this->priceIncTax(),
            'currency' => config('shop.currency'),
            'sku' => $this->sku,
            'published' => (bool) $this->published
        ];
    }
}
