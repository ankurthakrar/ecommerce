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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('category_id')->nullable();
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
            $table->string('brand')->nullable();
            $table->string('version')->nullable();
            $table->longText('tags')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description1')->nullable();
            $table->longText('description2')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_varient')->default(0);
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
        Schema::dropIfExists('products');
    }
};
