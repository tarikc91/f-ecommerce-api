<?php

namespace Tests\Feature;

use App\Models\PriceList;
use App\Models\PriceListProduct;
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

    public function test_get_products_order_by_price(): void
    {
        $product1 = Product::factory()->create([
            'published' => true,
            'price' => 10
        ]);

        $product2 = Product::factory()->create([
            'published' => true,
            'price' => 20
        ]);

        // ASC
        $response = $this->getJson('/api/products?orderBy=price,asc')
            ->assertStatus(200);

        $products = $response->getData()->data->products;

        $this->assertEquals($product1->id, $products[0]->id);
        $this->assertEquals($product2->id, $products[1]->id);

        // DESC
        $response = $this->getJson('/api/products?orderBy=price,desc')
            ->assertStatus(200);

        $products = $response->getData()->data->products;

        $this->assertEquals($product2->id, $products[0]->id);
        $this->assertEquals($product1->id, $products[1]->id);
    }

    public function test_get_products_order_by_price_with_price_overrides(): void
    {
        $product1 = Product::factory()->create([
            'price' => 50,
            'published' => true
        ]);

        $product2 = Product::factory()->create([
            'price' => 30,
            'published' => true
        ]);

        $priceList = PriceList::factory()->create();
        $priceList->products()
            ->attach($product1->id, [
                'price' => 25
            ]);

        // ASC
        $response = $this->getJson('/api/products?orderBy=price,asc', ['X-Price-List' => $priceList->id])
            ->assertStatus(200);

        $products = $response->getData()->data->products;

        $this->assertEquals($product1->id, $products[0]->id);
        $this->assertEquals($product2->id, $products[1]->id);

        // DESC
        $response = $this->getJson('/api/products?orderBy=price,desc', ['X-Price-List' => $priceList->id])
            ->assertStatus(200);

        $products = $response->getData()->data->products;

        $this->assertEquals($product2->id, $products[0]->id);
        $this->assertEquals($product1->id, $products[1]->id);
    }

    public function test_get_products_order_by_name(): void
    {
        $product1 = Product::factory()->create([
            'name' => 'A Product',
            'published' => true
        ]);

        $product2 = Product::factory()->create([
            'name' => 'B Product',
            'published' => true
        ]);

        // ASC
        $response = $this->getJson('/api/products?orderBy=name,asc')
            ->assertStatus(200);

        $products = $response->getData()->data->products;

        $this->assertEquals($product1->id, $products[0]->id);
        $this->assertEquals($product2->id, $products[1]->id);

        // DESC
        $response = $this->getJson('/api/products?orderBy=name,desc')
            ->assertStatus(200);

        $products = $response->getData()->data->products;

        $this->assertEquals($product2->id, $products[0]->id);
        $this->assertEquals($product1->id, $products[1]->id);
    }
}
