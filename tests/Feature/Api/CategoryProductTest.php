<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Api\Responses\ProductResponse;

class CategoryProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_does_not_exist(): void
    {
        $this->getJson('/api/categories/5/products')
            ->assertStatus(404);
    }

    public function test_get_category_products(): void
    {
        $category = Category::factory()->create();
        Product::factory(10)->hasAttached($category)->create(['published' => true]);
        Product::factory(10)->hasAttached($category)->create(['published' => false]);

        $this->getJson("/api/categories/{$category->id}/products")
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
}
