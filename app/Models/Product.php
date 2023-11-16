<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\UrlQueryFilters\Filters\ProductNameFilter;
use App\UrlQueryFilters\Filters\ProductOrderByName;
use App\UrlQueryFilters\Filters\ProductPriceFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'prices',
            function (Builder $builder) {
                $builder->withPriceListPrice(1)->withContractPrice();
            }
        );
    }

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
     * Scope that fetches the price for the price list id
     *
     * @param Builder $query
     * @param integer $priceListId
     * @return void
     */
    public function scopeWithPriceListPrice(Builder $query, int $priceListId): void
    {
        $query->addSelect([
            'price_list_price' => PriceListProduct::select('price')
                ->whereColumn('product_id', 'products.id')
                ->where('price_list_id', $priceListId)
                ->take(1)
        ]);
    }

    /**
     * Scope that fetches the price for the auth user
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWithContractPrice(Builder $query): void
    {
        if(auth()->guest()) {
            return;
        }

        $query->addSelect([
            'contract_price' => ContractListProduct::select('price')
                ->whereColumn('product_id', 'products.id')
                ->where('user_id', auth()->id())
                ->take(1)
        ]);
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

    /**
     * Default product price excluding tax
     *
     * @return float
     */
    public function defaultPriceExTax(): float
    {
        return (float) ($this->price / 100);
    }

    /**
     * Default product price including tax
     *
     * @return float
     */
    public function defaultPriceIncTax(): float
    {
        return (float) ($this->defaultPriceExTax() * config('shop.tax_rate'));
    }

    /**
     * Product price calculated by contract price and price list price excluding taxes
     * Contract price has priority over price list price
     *
     * @return float|null
     */
    public function calculatedPriceExTax(): ?float
    {
        $price = $this->contract_price ?? $this->price_list_price ?? null;

        return isset($price) ? (float) ($price / 100) : null;
    }

    /**
     * Product price calculated by contract price and price list price including taxes
     * Contract price has priority over price list price
     *
     * @return float|null
     */
    public function calculatedPriceIncTax(): ?float
    {
        return $this->calculatedPriceExTax() ?
            (float) ($this->calculatedPriceExTax() * config('shop.tax_rate')) :
            null;
    }

    /**
     * Final price excluding taxes
     *
     * @return float
     */
    public function priceExTax(): float
    {
        return $this->calculatedPriceExTax() ?? $this->defaultPriceExTax();
    }

    /**
     * Final price including taxes
     *
     * @return float
     */
    public function priceIncTax(): float
    {
        return (float) ($this->priceExTax() * config('shop.tax_rate'));
    }
}
