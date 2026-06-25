<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    function products(){
        return $this->hasMany(Product::Class);
    }
}
