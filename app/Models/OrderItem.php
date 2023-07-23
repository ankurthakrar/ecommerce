<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'product_variation_id',
        'is_booking_price',
        'qty',
        'title',
        'category_id',
        'final_price',
        'discount',
        'tax',
        'discount_amount',
        'tax_amount',
        'original_price',
        'pay_booking_price',
        'pay_booking_price_tax',
        'unit',
        'weight',
        'stock',
        'minimum_stock',
        'brand',
        'version',
        'tags',
        'description',
        'description1',
        'description2',
    ];
}
