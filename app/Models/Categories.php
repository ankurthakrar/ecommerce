<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

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
