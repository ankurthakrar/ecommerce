<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_address';

    protected $fillable = [
        'user_id',
        'address_line_1',
        'address_line_2',
        'city_id',
        'state_id',
        'pincode',
        'address_type',
    ];
}
