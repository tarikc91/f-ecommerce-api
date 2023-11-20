<?php

namespace App\QueryScopes;

use Illuminate\Support\Facades\DB;
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
        if (is_null($query->getQuery()->columns)) {
            $query->select($this->qualifyColumn('*'));
        }

        $query->leftJoin('price_list_product', function ($join) {
            $join->on('price_list_product.product_id', '=', 'products.id')
                ->on('price_list_product.price_list_id', '=', DB::raw(request()->header('X-Price-List') ?? 'null'));
        });

        $query->addSelect('price_list_product.price as price_list_price');
    }

    /**
     * Scope that fetches the price based on the contract list
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWithContractPrice(Builder $query): void
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select($this->qualifyColumn('*'));
        }

        $query->leftJoin('contract_list_product', function ($join) {
            $join->on('contract_list_product.product_id', '=', 'products.id')
                ->on('contract_list_product.user_id', '=', DB::raw(auth('sanctum')->id() ?? 'null'));
        });

        $query->addSelect('contract_list_product.price as contract_price');
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
                WHEN `products`.price > 0 THEN `products`.price
            END) as `final_price`")
        );
    }
}
