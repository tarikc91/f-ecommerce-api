<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::chunk(100, function ($categories) {
            foreach ($categories as $category) {
                Product::factory()->hasAttached($category)->count(10)->create();
            }
        });
    }
}
