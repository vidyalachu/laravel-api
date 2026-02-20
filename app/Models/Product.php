<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'status',
        'category_id',
        'image',
    ];


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category(){
        return $this->belongsTo(category::class);
    }

}
