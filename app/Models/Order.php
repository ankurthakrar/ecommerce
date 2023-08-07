<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'address_id',
        'total_amount',
        'order_status',
        'payment_method',
        'payment_status',
        'payment_id',
        'address_line_1',
        'address_line_2',
        'city_id',
        'state_id',
        'pincode',
        'address_type',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
