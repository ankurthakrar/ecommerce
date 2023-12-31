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
        'full_name',
        'email',
        'phone_no',
        'address_line_1',
        'address_line_2',
        'city_id',
        'state_id',
        'pincode',
        'address_type',
    ];

    // APPEND
    protected $appends = ['state_name','city_name'];

    public function getStateNameAttribute()
    {
        if (!empty($this->state_id)) {
            return State::where('id',$this->state_id)->pluck('name')->first();
        }
        return null;
    }
   
    public function getCityNameAttribute()
    {
        if (!empty($this->city_id)) {
            return City::where('id',$this->city_id)->pluck('name')->first();
        }
        return null;
    }
}
