<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\PriceList;
use Illuminate\Database\Seeder;

class ProductRelationshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $priceLists = PriceList::all();

        Product::limit(500)->chunk(100, function ($products) use ($categories, $priceLists) {
            foreach ($products as $product) {
                $product
                    ->categories()
                    ->attach($categories->random()->id);

                $product
                    ->priceLists()
                    ->attach(
                        $priceLists->random()->id,
                        [
                            'price' => fake()->numberBetween(100, 1000)
                        ]
                    );
            }
        });
    }
}
