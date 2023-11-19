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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('street_address');
            $table->string('city');
            $table->string('country');
            $table->unsignedDecimal('subtotal_price_ex_tax', 8, 4);
            $table->unsignedDecimal('subtotal_price_inc_tax', 8, 4);
            $table->unsignedDecimal('subtotal_tax', 8, 4);
            $table->unsignedDecimal('tax_rate', 8, 4);
            $table->unsignedDecimal('discount_percentage', 8, 4);
            $table->unsignedDecimal('discount_amount', 8, 4);
            $table->unsignedDecimal('total_price_ex_tax', 8, 4);
            $table->unsignedDecimal('total_price_inc_tax', 8, 4);
            $table->unsignedDecimal('total_tax', 8, 4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
