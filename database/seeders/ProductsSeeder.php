<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for($i = 0; $i < 50000; $i++) {
            $data[] = array_merge(
                Product::factory()->make()->toArray(),
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        foreach(array_chunk($data, 1000) as $chunk) {
            Product::insert($chunk);
        }
    }
}
