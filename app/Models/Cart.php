<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variation_id',
        'qty',
    ];

    public function getCartImageUrlAttribute()
    {
        if ($this->is_variant == 1) {
            // Retrieve image URL from variantImages relationship
            if ($this->image_url) {
                $imageUrl = URL('/public/product_variant_image/' . $this->image_url);
            } else {
                $imageUrl = URL('/public/static_image/product_static_image.jpg');
            }
        } else {
            // Retrieve image URL from images relationship
            if ($this->image_url) {
                $imageUrl = URL('/public/product_image/' . $this->image_url);
            } else {
                $imageUrl = URL('/public/static_image/product_static_image.jpg');
            }
        }
        unset($this->variantImages);
        unset($this->images);
        return $imageUrl;
    }
}
