<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' =>'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:today'
        ]);

        // Extra validation for percentage
        if ($request->type === 'percentage' && $request->value > 100) {
            return response()->json([
                'status' => false,
                'message' => 'Percentage discount cannot exceed 100%'
            ], 400);
        }

        $coupon = Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'usage_limit' => $request->usage_limit,
            'per_user_limit' => $request->per_user_limit,
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => $request->expires_at
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon created successfully',
            'data' => $coupon
        ]);
    }


    public function index()
    {
        return Coupon::all();
    }
}
