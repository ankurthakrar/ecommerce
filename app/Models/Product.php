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
        'final_price',
        'discount',
        'tax',
        'discount_amount',
        'tax_amount',
        'original_price',
        'unit',
        'weight',
        'stock',
        'minimum_stock',
        'tags',
        'description',
        'description1',
        'description2',
        'is_active',
        'is_varient',
    ];

   // APPEND
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->images->isNotEmpty()) {
            $imageUrls = $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => URL('/product_image/' . $image->file_name),
                ];
            });
        } else {
            $imageUrls = [
                [
                    'id' => null,
                    'url' => URL('/static_image/product_static_image.jpg'),
                ],
            ];
        }
        unset($this->images);
        return $imageUrls;
    }

    // IMAGES RELATIONSHIP
    public function images()
    {
        return $this->hasMany(Image::class, 'type_id')->where('type', 'product_image');
    }

    //IT WILL RETURN VARIANT
    public function variant()
    {
        return $this->hasMany(ProductVariant::class, 'product_id')->with('variantImages');
    }
}
