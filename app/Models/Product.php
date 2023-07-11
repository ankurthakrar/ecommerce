<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'new_price',
        'discount',
        'tax',
        'original_price',
        'minimum_stock',
        'tags',
        'description',
        'description1',
        'description2'
    ];
}
