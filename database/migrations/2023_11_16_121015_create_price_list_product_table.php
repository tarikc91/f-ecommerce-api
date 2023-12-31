<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_list_product', function (Blueprint $table) {
            $table->unsignedInteger('price_list_id');
            $table->unsignedInteger('product_id');
            $table->unsignedDecimal('price', 8, 4)->index();

            $table->primary(['price_list_id', 'product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('price_list_id')->references('id')->on('price_lists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_product');
    }
};
