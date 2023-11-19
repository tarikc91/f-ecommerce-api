<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'street_address',
        'city',
        'country',
        'subtotal_price_ex_tax',
        'subtotal_price_inc_tax',
        'subtotal_tax',
        'tax_rate',
        'discount_percentage',
        'discount_amount',
        'total_price_ex_tax',
        'total_price_inc_tax',
        'total_tax'
    ];

    /**
     * Order products relationship
     *
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Products relationship
     *
     * @return HasMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot([
                'product_id',
                'quantity',
                'price_ex_tax',
                'price_inc_tax',
                'price_tax',
                'total_price_ex_tax',
                'total_price_inc_tax'
            ]);
    }
}
