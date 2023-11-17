<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\PriceList;
use Illuminate\Database\Seeder;
use App\Models\CategoryProduct;
use App\Models\PriceListProduct;

class ProductRelationshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect(Category::all()->modelKeys());
        $priceLists = collect(PriceList::all()->modelKeys());

        Product::withoutGlobalScopes()
            ->limit(40000)->chunk(1000, function ($products) use ($categories, $priceLists) {
                $categoriesData = [];
                $priceListsData = [];

                foreach ($products as $product) {
                    $categoriesData[] = [
                        'product_id' => $product->id,
                        'category_id' => $categories->random()
                    ];

                    $priceListsData[] = [
                        'product_id' => $product->id,
                        'price_list_id' => $priceLists->random(),
                        'price' => fake()->numberBetween(100, 1000)
                    ];
                }

                CategoryProduct::insert($categoriesData);
                PriceListProduct::insert($priceListsData);
            });
    }
}
