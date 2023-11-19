<?php

namespace App\QueryScopes;

use App\Models\PriceListProduct;
use Illuminate\Support\Facades\DB;
use App\Models\ContractListProduct;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait ProductScopes
{
    /**
     * Scope that fetches published products
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWherePublished(Builder $query): void
    {
        $query->where('published', true);
    }

    /**
     * Scope that fetches the price based on the price list id
     *
     * @param Builder $query
     * @param integer $priceListId
     * @return void
     */
    public function scopeWithPriceListPrice(Builder $query): void
    {
        $query->addSelect([
            'price_list_price' => PriceListProduct::select('price')
                ->whereColumn('product_id', 'products.id')
                ->where('price_list_id', request()->header('X-Price-List') ?? null)
                ->take(1)
        ]);
    }

    /**
     * Scope that fetches the price based on the contract list
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWithContractPrice(Builder $query): void
    {
        $query->addSelect([
            'contract_price' => ContractListProduct::select('price')
                ->whereColumn('product_id', 'products.id')
                ->where('user_id', auth('sanctum')->id())
                ->take(1)
        ]);
    }

    /**
     * Scope that fetches the final price for the product
     * takin into account all available prices
     * Order of importance: contract_price, price_list_price, price
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWithFinalPrice(Builder $query): void
    {
        $query->addSelect(
            DB::raw("
            (SELECT CASE 
                WHEN `contract_price` > 0 THEN `contract_price`
                WHEN `price_list_price` > 0 THEN `price_list_price`
                WHEN `price` > 0 THEN `price`
            END) as `final_price`")
        );
    }
}
