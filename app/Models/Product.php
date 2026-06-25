<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    function brand(){
        return $this->belongsTo(Brand::class);
    }

    function category(){
        return $this->belongsTo(Category::class);
    }
}
