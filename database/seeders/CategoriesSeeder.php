<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainCategories = Category::factory()->count(5)->create();

        foreach ($mainCategories as $mainCategory) {
            $categories = Category::factory()->count(5)->for($mainCategory, 'parent')->create();

            foreach ($categories as $category) {
                Category::factory()->count(5)->for($category, 'parent')->create();
            }
        }
    }
}
