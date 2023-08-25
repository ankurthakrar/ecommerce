<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'user_id',
        'title',
        'doc_name',
    ];

    // APPEND
    protected $appends = ['doc_url','type'];

    public function getDocUrlAttribute()
    {
        if ($this->doc_name !== '') {
            return  URL('/public/user_document/' . $this->doc_name);
        }
        return '';
    }

    public function getTypeAttribute()
    {
        if ($this->doc_name !== '') {
            $extension = pathinfo($this->doc_name, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $type = 'image';
            } elseif ($extension === 'pdf') {
                $type = 'document';
            } else {
                $type = 'unknown';
            }
            return  $type;
        }
        return '';
    }
}
