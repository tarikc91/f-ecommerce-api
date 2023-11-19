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
            'id' => $this->id,
            'name' => $this->name,
            'description'  => $this->description,
            'price_ex_tax' => $this->priceExTax(),
            'price_inc_tax' => $this->priceIncTax(),
            'price_list_price_ex_tax' => $this->priceListPriceExTax(),
            'price_list_price_inc_tax' => $this->priceListPriceIncTax(),
            'contract_price_ex_tax' => $this->contractPriceExTax(),
            'contract_price_inc_tax' => $this->contractPriceIncTax(),
            'final_price_ex_tax' => $this->finalPriceExTax(),
            'final_price_inc_tax' => $this->finalPriceIncTax(),
            'currency' => config('shop.currency'),
            'sku' => $this->sku,
            'published' => (bool) $this->published
        ];
    }
}
