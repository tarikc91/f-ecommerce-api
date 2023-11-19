<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'street_address' => $this->street_address,
            'city' => $this->city,
            'country' => $this->country,
            'subtotal_price_ex_tax' => $this->subtotal_price_ex_tax,
            'subtotal_price_inc_tax' => $this->subtotal_price_inc_tax,
            'subtotal_tax' => $this->subtotal_tax,
            'tax_rate' => $this->tax_rate,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => $this->discount_amount,
            'total_price_ex_tax' => $this->total_price_ex_tax,
            'total_price_inc_tax' => $this->total_price_inc_tax,
            'total_tax' => $this->total_tax,
            'orderProducts' => OrderProductResource::collection($this->whenLoaded('orderProducts'))
        ];
    }
}
