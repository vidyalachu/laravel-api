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
        'slug',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when(!empty($filters['status']),
            fn ($q) => $q->where('status', $filters['status'])
        );

        $query->when(!empty($filters['min_price']),
            fn ($q) => $q->where('price', '>=', $filters['min_price'])
        );

        $query->when(!empty($filters['max_price']),
            fn ($q) => $q->where('price', '<=', $filters['max_price'])
        );
    }

    public function scopeSort($query, ?string $sort)
    {
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('created_at', 'desc'), // default latest
        };
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category(){
        return $this->belongsTo(category::class);
    }

}
