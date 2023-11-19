<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price_ex_tax' => $this->price_ex_tax,
            'price_inc_tax' => $this->price_inc_tax,
            'price_tax' => $this->price_tax,
            'total_price_ex_tax' => $this->total_price_ex_tax,
            'total_price_inc_tax' => $this->total_price_inc_tax,
            'total_price_tax' => $this->total_price_tax
        ];
    }
}
