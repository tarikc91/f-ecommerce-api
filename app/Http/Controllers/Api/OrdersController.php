<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\CreateOrderRequest;

class OrdersController extends Controller
{
    /**
     * Stores a new order
     *
     * @param CreateOrderRequest $request
     * @return OrderResource
     */
    public function store(CreateOrderRequest $request): OrderResource
    {
        $products = [];
        $subtotalPriceExTax = 0;

        foreach ($request->products as $orderedProductData) {
            $product = Product::find($orderedProductData['id']);
            $product->quantity = $orderedProductData['quantity'];
            $products[] = $product;
            $subtotalPriceExTax += $product->quantity * $product->finalPriceExTax();
        }

        $discountAmount = hasDiscount($subtotalPriceExTax) ? discountAmount($subtotalPriceExTax) : 0;
        $totalPriceExTax = $subtotalPriceExTax - $discountAmount;

        $order = Order::create([
            'user_id' => auth('sanctum')->id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'street_address' => $request->street_address,
            'city' => $request->city,
            'country' => $request->country,
            'tax_rate' => config('shop.tax_rate'),
            'subtotal_price_ex_tax' => $subtotalPriceExTax,
            'subtotal_price_inc_tax' => addTax($subtotalPriceExTax),
            'subtotal_tax' => taxDiff($subtotalPriceExTax),
            'discount_percentage' => hasDiscount($subtotalPriceExTax) ? config('shop.discount_threshold_percentage') : 0,
            'discount_amount' => $discountAmount,
            'total_price_ex_tax' => $totalPriceExTax,
            'total_price_inc_tax' => addTax($totalPriceExTax),
            'total_tax' => taxDiff($totalPriceExTax)
        ]);

        foreach ($products as $product) {
            $productFinalPriceExTax = $product->finalPriceExTax();
            $productTotalPriceExTax = $productFinalPriceExTax * $product->quantity;

            $order->orderProducts()->create([
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'price_ex_tax' => $productFinalPriceExTax,
                'price_inc_tax' => addTax($productFinalPriceExTax),
                'price_tax' => taxDiff($productFinalPriceExTax),
                'total_price_ex_tax' => $productTotalPriceExTax,
                'total_price_inc_tax' => addTax($productTotalPriceExTax),
                'total_price_tax' => taxDiff($productTotalPriceExTax)
            ]);
        }

        $order->load('orderProducts');

        return new OrderResource($order);
    }
}
