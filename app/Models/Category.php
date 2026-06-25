<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name','slug','status','image','parent_id'])]

class Category extends Model
{
    function parent(){
        return $this->belongsTo(Category::class,'parent_id');
    }

    function children(){
        return $this->hasMany(Category::class,'parent_id');
    }

    function products(){
        return $this->hasMany(Product::Class);
    }
}
