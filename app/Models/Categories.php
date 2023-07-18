<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'is_active'
    ];

    // APPEND
    protected $appends = ['image_url','cat_type'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            $imageUrl = URL('/category_image/' . $this->image->file_name);
        }else{
            $imageUrl = URL('/static_image/category_static_image.png');
        }
        unset($this->image);
        return $imageUrl;
    }

    public function getCatTypeAttribute()
    {
        if ($this->parent_id == 0) {
            unset($this->parent);
           return "Parent";
        }elseif ($this->parent_id != 0 && $this->parent->parent_id == 0) {
            unset($this->parent);
            return "Child";
        } else {
            unset($this->parent);
            return "Subchild";
        }
        return null;
    }

    // IT WILL CATEGORY IMAGE
    public function image()
    {
        return $this->hasOne(Image::class, 'type_id')->where('type', 'category_image');
    }

    // IT WILL RETURN ONLY ONE CHILDREN
    public function children()
    {
        return $this->hasMany(Categories::class, 'parent_id');
    }

    //IT WILL RETURN MULTIPLE CHILDREN
    public function childrens()
    {
        return $this->hasMany(Categories::class, 'parent_id')->with('childrens');
    }

    // IT WILL RETURN ONLY ONE PARENT
    public function parent()
    {
        return $this->belongsTo(Categories::class, 'parent_id');
    }

    //IT WILL RETURN MULTIPLE PARENT
    public function parents()
    {
        return $this->belongsTo(Categories::class, 'parent_id')->with('parents');
    }

}
