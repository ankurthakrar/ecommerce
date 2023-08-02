<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'file_name',
        'type',
        'custom_data'
    ];

    protected $appends = ['image_url'];
    
    protected $casts = [
        'custom_data' => 'array',
    ];
    // ACCESSOR

    public function getImageUrlAttribute()
    {
        return asset($this->type .'/'. $this->file_name);
    }
}
