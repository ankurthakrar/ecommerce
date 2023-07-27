<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active'
    ];

    // APPEND
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return [
                'id' => $this->image->id,
                'url' => URL('/public/brand_image/' . $this->image->file_name),
            ];
        }
        return null;
    }

    // IMAGES RELATIONSHIP
    public function image()
    {
        return $this->hasOne(Image::class, 'type_id')->where('type', 'brand_image');
    }
}
