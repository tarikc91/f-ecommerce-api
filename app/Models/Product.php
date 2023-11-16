<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\UrlQueryFilters\Filters\ProductNameFilter;
use App\UrlQueryFilters\Filters\ProductOrderByName;
use App\UrlQueryFilters\Filters\ProductPriceFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\UrlQueryFilters\Filters\ProductCategoriesFilter;
use App\UrlQueryFilters\Filterable as UrlQueryFilterable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\UrlQueryFilters\Filters\ProductOrderByPriceFilter;

class Product extends Model
{
    use HasFactory;
    use UrlQueryFilterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'published'
    ];

    /**
     * The filters used for url query filtering
     *
     * @var array<string,object>
     */
    protected static array $urlQueryFilters = [
        'categories' => ProductCategoriesFilter::class,
        'name' => ProductNameFilter::class,
        'price' => ProductPriceFilter::class,
        'orderbyname' => ProductOrderByName::class,
        'orderbyprice' => ProductOrderByPriceFilter::class
    ];

    /**
     * Gets all categories of a product
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Gets all price lists for the product
     *
     * @return BelongsToMany
     */
    public function priceLists(): BelongsToMany
    {
        return $this->belongsToMany(PriceList::class)
            ->using(PriceListProduct::class)
            ->withPivot('price');
    }
}
