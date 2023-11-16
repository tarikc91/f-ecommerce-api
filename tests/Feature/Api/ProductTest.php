<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Api\Responses\ProductResponse;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_no_product(): void
    {
        $this->getJson('/api/products/1')
            ->assertStatus(404);
    }

    public function test_show_product(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/api/products/{$product->id}")
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->has(
                    'data',
                    fn(AssertableJson $json) =>
                        $json->whereAllType(ProductResponse::getJsonAttributes())
                )
            );
    }

    public function test_get_no_products(): void
    {
        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->hasAll(['data', 'links', 'meta'])
                    ->has('data.products', 0)
                    ->missing('data.products.0')
            );
    }

    public function test_get_products(): void
    {
        // Prepare some products
        Product::factory(30)->create();

        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->hasAll(['data', 'links', 'meta'])
                    ->has('data.products', 25)
                    ->has(
                        'data.products.0',
                        fn(AssertableJson $json) =>
                        $json->whereAllType(ProductResponse::getJsonAttributes())
                    )
            );
    }
}
