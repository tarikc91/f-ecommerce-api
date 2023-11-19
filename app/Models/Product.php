<?php

namespace App\Models;

use App\QueryScopes\ProductScopes;
use Illuminate\Database\Eloquent\Model;
use App\UrlQueryFilters\Filters\ProductNameFilter;
use App\UrlQueryFilters\Filters\ProductPriceFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\UrlQueryFilters\Filters\ProductCategoriesFilter;
use App\UrlQueryFilters\Filters\ProductOrderByNameFilter;
use App\UrlQueryFilters\Filterable as UrlQueryFilterable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\UrlQueryFilters\Filters\ProductOrderByPriceFilter;

class Product extends Model
{
    use HasFactory;
    use ProductScopes;
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
        'name' => ProductNameFilter::class,
        'price' => ProductPriceFilter::class,
        'categories' => ProductCategoriesFilter::class,
        'orderbyname' => ProductOrderByNameFilter::class,
        'orderbyprice' => ProductOrderByPriceFilter::class
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'prices',
            fn(Builder $builder) => $builder
                ->withPriceListPrice()
                ->withContractPrice()
                ->withFinalPrice()
        );
    }

    /**
     * Categories relationship
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Price lists relationship
     *
     * @return BelongsToMany
     */
    public function priceLists(): BelongsToMany
    {
        return $this->belongsToMany(PriceList::class)
            ->using(PriceListProduct::class)
            ->withPivot('price');
    }

    /**
     * Price excluding tax
     *
     * @return float|null
     */
    public function priceExTax(): ?float
    {
        return $this->price ?? null;
    }

    /**
     * Price including tax
     *
     * @return float|null
     */
    public function priceIncTax(): ?float
    {
        return $this->priceExTax() ? addTax($this->priceExTax()) : null;
    }

    /**
     * Price list price excluding tax
     *
     * @return float|null
     */
    public function priceListPriceExTax(): ?float
    {
        return $this->price_list_price ?? null;
    }

    /**
     * Price list price including tax
     *
     * @return float|null
     */
    public function priceListPriceIncTax(): ?float
    {
        return $this->priceListPriceExTax() ? addTax($this->priceListPriceExTax()) : null;
    }

    /**
     * Contract price excluding tax
     *
     * @return float|null
     */
    public function contractPriceExTax(): ?float
    {
        return $this->contract_price ?? null;
    }

    /**
     * Contract price including tax
     *
     * @return float|null
     */
    public function contractPriceIncTax(): ?float
    {
        return $this->contractPriceExTax() ? addTax($this->contractPriceExTax()) : null;
    }

    /**
     * Final price excluding tax
     *
     * @return float|null
     */
    public function finalPriceExTax(): ?float
    {
        return $this->final_price ?? null;
    }

    /**
     * Final price including tax
     *
     * @return float|null
     */
    public function finalPriceIncTax(): ?float
    {
        return $this->finalPriceExTax() ? addTax($this->finalPriceExTax()) : null;
    }
}
