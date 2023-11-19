<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProduct extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'price_ex_tax',
        'price_inc_tax',
        'price_tax',
        'total_price_ex_tax',
        'total_price_inc_tax',
        'total_price_tax'
    ];
}
