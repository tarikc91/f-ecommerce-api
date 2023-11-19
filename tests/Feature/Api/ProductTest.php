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

    public function test_can_show_published_product(): void
    {
        $product = Product::factory()->create(['published' => true]);

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

    public function test_cant_show_unpublished_product(): void
    {
        $product = Product::factory()->create(['published' => false]);

        $this->getJson("/api/products/{$product->id}")
            ->assertStatus(404);
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
        Product::factory(10)->create(['published' => true]);
        Product::factory(10)->create(['published' => false]);

        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->hasAll(['data', 'links', 'meta'])
                    ->has('data.products', 10)
                    ->has(
                        'data.products.0',
                        fn(AssertableJson $json) =>
                        $json->whereAllType(ProductResponse::getJsonAttributes())
                    )
            );
    }

    // TODO: tests for product filters and sorting
    // TODO: tests for price list header and user contract
}
