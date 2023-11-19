<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\PriceList;
use App\Models\ContractListProduct;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Api\Responses\OrderResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_posting_order_has_validation(): void
    {
        $this->postJson('/api/orders')
            ->assertStatus(422)
            ->assertJson(
                fn(AssertableJson $json) =>
                    $json->hasAll([
                        'message',
                        'errors.first_name',
                        'errors.last_name',
                        'errors.email',
                        'errors.phone',
                        'errors.street_address',
                        'errors.city',
                        'errors.country',
                        'errors.products'
                    ])
            );
    }

    public function test_posting_order_products_need_to_exist_validation(): void
    {
        $this->postJson('/api/orders', $this->setOrderData([
            'products' => [
                [
                    'id' => 4,
                    'quantity' => 2
                ],
                [
                    'id' => 5,
                    'quantity' => 2
                ]
            ]
        ]))
        ->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) =>
                $json->missingAll([
                    'errors.first_name',
                    'errors.last_name',
                    'errors.email',
                    'errors.phone',
                    'errors.street_address',
                    'errors.city',
                    'errors.country'
                ])->hasAll([
                    'message',
                    'errors'
                ])
        );
    }

    public function test_posting_order_products_need_to_be_published_validation(): void
    {
        $product = Product::factory()->create(['published' => false]);

        $this->postJson('/api/orders', $this->setOrderData([
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]))
        ->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) =>
                $json->missingAll([
                    'errors.first_name',
                    'errors.last_name',
                    'errors.email',
                    'errors.phone',
                    'errors.street_address',
                    'errors.city',
                    'errors.country'
                ])->hasAll([
                    'message',
                    'errors'
                ])
        );
    }

    public function test_posting_order_max_quantity_validation(): void
    {
        $product = Product::factory()->create(['published' => true]);

        $this->postJson('/api/orders', $this->setOrderData([
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 55
                ]
            ]
        ]))
        ->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) =>
                $json->missingAll([
                    'errors.first_name',
                    'errors.last_name',
                    'errors.email',
                    'errors.phone',
                    'errors.street_address',
                    'errors.city',
                    'errors.country'
                ])->hasAll([
                    'message',
                    'errors'
                ])
        );
    }

    public function test_can_make_order_without_discount(): void
    {
        $product1 = Product::factory()->create([
            'published' => true,
            'price' => 5.5
        ]);

        $product2 = Product::factory()->create([
            'published' => true,
            'price' => 10.5
        ]);

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('order_product');

        $this->makeOrderPostRequest([
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 1
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 3
                ]
            ]
        ]);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(2, OrderProduct::count());

        $order = Order::first();
        $this->assertOrderTable($order, 37);
        $this->assertOrderProductsTable($order, [
            [
                'id' => $product1->id,
                'finalPriceExTax' => $product1->price,
                'quantity' => 1
            ],
            [
                'id' => $product2->id,
                'finalPriceExTax' => $product2->price,
                'quantity' => 3
            ]
        ]);
    }

    public function test_can_make_order_with_discount(): void
    {
        $product1 = Product::factory()->create([
            'published' => true,
            'price' => 50
        ]);

        $product2 = Product::factory()->create([
            'published' => true,
            'price' => 10.5
        ]);

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('order_product');

        $this->makeOrderPostRequest([
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 2
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 1
                ]
            ]
        ]);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(2, OrderProduct::count());

        $order = Order::first();
        $this->assertOrderTable($order, 110.5);
        $this->assertOrderProductsTable($order, [
            [
                'id' => $product1->id,
                'finalPriceExTax' => $product1->price,
                'quantity' => 2
            ],
            [
                'id' => $product2->id,
                'finalPriceExTax' => $product2->price,
                'quantity' => 1
            ]
        ]);
    }

    public function test_can_make_order_with_price_list(): void
    {
        $product1 = Product::factory()->create([
            'published' => true,
            'price' => 50
        ]);

        $product2 = Product::factory()->create([
            'published' => true,
            'price' => 10.5
        ]);

        PriceList::factory()
            ->create()
            ->products()
            ->attach($product1->id, ['price' => 25]);

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('order_product');

        $this->makeOrderPostRequest([
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 2
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 1
                ]
            ]
        ]);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(2, OrderProduct::count());

        $order = Order::first();
        $this->assertOrderTable($order, 60.5);
        $this->assertOrderProductsTable($order, [
            [
                'id' => $product1->id,
                'finalPriceExTax' => 25,
                'quantity' => 2
            ],
            [
                'id' => $product2->id,
                'finalPriceExTax' => $product2->price,
                'quantity' => 1
            ]
        ]);
    }

    public function test_can_make_order_with_contract_list(): void
    {
        $user = User::factory()->create();

        $product1 = Product::factory()->create([
            'published' => true,
            'price' => 50
        ]);

        $product2 = Product::factory()->create([
            'published' => true,
            'price' => 10.5
        ]);

        PriceList::factory()
            ->create()
            ->products()
            ->attach($product1->id, ['price' => 25]);

        ContractListProduct::create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'price' => 20
        ]);

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('order_product');

        $this->makeOrderPostRequest([
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 2
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 1
                ]
            ]
        ], $user);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(2, OrderProduct::count());

        $order = Order::first();
        $this->assertOrderTable($order, 50.5, $user);
        $this->assertOrderProductsTable($order, [
            [
                'id' => $product1->id,
                'finalPriceExTax' => 20,
                'quantity' => 2
            ],
            [
                'id' => $product2->id,
                'finalPriceExTax' => $product2->price,
                'quantity' => 1
            ]
        ]);
    }

    /**
     * Makes a request to the orders endpoint
     *
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    public function makeOrderPostRequest(array $data, ?User $user = null): TestResponse
    {
        $request = $user ? $this->actingAs($user, 'sanctum') : $this;

        return $request->postJson(
            '/api/orders', 
            $this->setOrderData($data),
            ['X-Price-List' => 1]
        )
        ->assertStatus(201)
        ->assertJson(
            fn(AssertableJson $json) =>
            $json->has(
                'data',
                fn(AssertableJson $json) =>
                    $json->whereAllType(OrderResponse::getJsonAttributes())
            )
        );
    }

    /**
     * Sets data
     *
     * @param array $data
     * @return array
     */
    public function setOrderData(array $data): array
    {
        return array_merge(
            [
                'first_name' => 'Tarik',
                'last_name' => 'Coralic',
                'email' => 'tarik@gmail.com',
                'phone' => '123456789',
                'street_address' => 'Happy Street',
                'city' => 'Karlovac',
                'country' => 'Croatia'
            ],
            $data
        );
    }

    /**
     * Asserts orders tables
     *
     * @param Order $order
     * @param array $products
     * @param float $productsPriceSum
     * @param User|null $user
     * @return void
     */
    public function assertOrderTable(Order $order, float $productsPriceSum, ?User $user = null): void
    {
        $hasDiscount = $productsPriceSum > config('shop.discount_threshold_price');
        $discountAmount = $hasDiscount ? price($productsPriceSum)->discountAmount()->value() : 0;
        $totalPriceExTax = $productsPriceSum - $discountAmount;

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user?->id,
            'first_name' => 'Tarik',
            'last_name' => 'Coralic',
            'email' => 'tarik@gmail.com',
            'phone' => '123456789',
            'street_address' => 'Happy Street',
            'city' => 'Karlovac',
            'country' => 'Croatia',
            'tax_rate' => config('shop.tax_rate'),
            'subtotal_price_ex_tax' => $productsPriceSum,
            'subtotal_price_inc_tax' => addTax($productsPriceSum),
            'subtotal_tax' => taxDiff($productsPriceSum),
            'discount_percentage' => $hasDiscount ? config('shop.discount_threshold_percentage') : 0,
            'discount_amount' => $discountAmount,
            'total_price_ex_tax' => $totalPriceExTax,
            'total_price_inc_tax' => addTax($totalPriceExTax),
            'total_tax' => taxDiff($totalPriceExTax)
        ]);
    }

    /**
     * Asserts order_products tables
     *
     * @param Order $order
     * @param array $products
     * @return void
     */
    public function assertOrderProductsTable(Order $order, array $products): void
    {
        foreach($products as $product) {
            $totalPriceExTax = $product['finalPriceExTax'] * $product['quantity'];

            $this->assertDatabaseHas('order_product', [
                'product_id' => $product['id'],
                'order_id' => $order->id,
                'quantity' => $product['quantity'],
                'price_ex_tax' => $product['finalPriceExTax'],
                'price_inc_tax' => addTax($product['finalPriceExTax']),
                'price_tax' => taxDiff($product['finalPriceExTax']),
                'total_price_ex_tax' => $totalPriceExTax,
                'total_price_inc_tax' => addTax($totalPriceExTax),
                'total_price_tax' => taxDiff($totalPriceExTax)
            ]);
        }
    }
}
