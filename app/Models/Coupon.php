<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'per_user_limit',
        'used_count',
        'is_active',
        'expires_at'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
