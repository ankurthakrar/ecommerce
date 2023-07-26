<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->nullable();
            $table->string('final_price')->nullable();
            $table->string('discount')->nullable();
            $table->string('tax')->nullable();
            $table->string('discount_amount')->nullable();
            $table->string('tax_amount')->nullable();
            $table->string('original_price')->nullable();
            $table->string('pay_booking_price')->nullable();
            $table->string('pay_booking_price_tax')->nullable();
            $table->string('sku')->nullable();
            $table->string('weight')->nullable();
            $table->string('stock')->nullable();
            $table->string('minimum_stock')->nullable();
            $table->integer('is_active')->default(1);
            $table->string('colour')->nullable();
            $table->string('color_name')->nullable();
            $table->string('size')->nullable();
            $table->string('available_in')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};
