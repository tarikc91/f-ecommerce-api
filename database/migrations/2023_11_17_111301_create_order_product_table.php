<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->unsignedDecimal('price_ex_tax', 8, 4);
            $table->unsignedDecimal('price_inc_tax', 8, 4);
            $table->unsignedDecimal('price_tax', 8, 4);
            $table->unsignedDecimal('total_price_ex_tax', 8, 4);
            $table->unsignedDecimal('total_price_inc_tax', 8, 4);
            $table->unsignedDecimal('total_price_tax', 8, 4);

            $table->unique(['order_id', 'product_id']);
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
