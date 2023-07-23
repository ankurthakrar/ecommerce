<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'product_id',
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
        'is_active',
    ];

    // APPEND
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->variantImages->isNotEmpty()) {
            $imageUrls = $this->variantImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => URL('/public/product_variant_image/' . $image->file_name),
                ];
            });
        } else {
            $imageUrls = [
                [
                    'id' => null,
                    'url' => URL('/public/static_image/product_static_image.jpg'),
                ],
            ];
        }
        unset($this->variantImages);
        return $imageUrls;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // IT WILL RETURN VARIANT IMAGES
    public function variantImages()
    {
        return $this->hasMany(Image::class, 'type_id')->where('type', 'product_variant_image');
    }
}
