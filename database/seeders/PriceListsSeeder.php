<?php

namespace Database\Seeders;

use App\Models\PriceList;
use Illuminate\Database\Seeder;

class PriceListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for($i = 0; $i < 10; $i++) {
            $data[] = array_merge(
                PriceList::factory()->make()->toArray(),
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        foreach(array_chunk($data, 1000) as $chunk) {
            PriceList::insert($chunk);
        }
    }
}
