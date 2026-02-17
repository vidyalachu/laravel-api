<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;

class OrderController extends Controller
{
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,ds,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($request->id);

        $order->update([
            'status' => $request->status
        ]);

        if($request->status == "cancelled"){
            foreach($order->items as $item){
                $item->product->increment('stock',$item->quantiy);
            }
        }

        return response()->json($order);
    }

    public function userOrderHistroy(){
        $order = Order::with('items.product')
                    ->where('user_id', auth()->id())
                    ->get();
        return response()->json($order);

    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {

            $total = 0;

            $coupon = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('is_active', true)
                    ->first();            
                

                if (!$coupon) {
                    throw new \Exception('Invalid coupon');
                }

                if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
                    throw new \Exception('Coupon expired');
                }

                if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                    throw new \Exception('Coupon usage limit reached');
                }

                $usage = DB::table('coupon_user')
                    ->where('coupon_id', $coupon->id)
                    ->where('user_id', auth()->id())
                    ->first();

                if ($usage && $usage->usage_count >= $coupon->per_user_limit) {
                    throw new \Exception('Coupon usage limit reached');
                }
            }


            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => 0,
                'status' => 'pending'
            ]);

            foreach ($request->items as $item) {

                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product {$product->name}");
                }

                $lineTotal = $product->price * $item['quantity'];
                $total += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $discount = 0;

            if ($coupon) {

                if ($total < $coupon->min_order_amount) {
                    throw new \Exception('Minimum order amount not reached');
                }

                if ($coupon->type == 'fixed') {
                    $discount = $coupon->value;
                }

                if ($coupon->type == 'percentage') {
                    $discount = ($total * $coupon->value) / 100;
                }
                $discount = min($discount, $total); // remove negative value

                $total = $total - $discount;
            }


            $order->update([
                            'total_amount' => $total,                            
                            'coupon_id' => $coupon?->id,
                            'discount_amount' => $discount
                            ]);

            if ($coupon) {
                $coupon->increment('used_count');
                $couponUser = DB::table('coupon_user')
                    ->where('coupon_id', $coupon->id)
                    ->where('user_id', auth()->id())
                    ->first();

                if ($couponUser) {
                    DB::table('coupon_user')
                        ->where('coupon_id', $coupon->id)
                        ->where('user_id', auth()->id())
                        ->increment('usage_count');
                } else {
                    DB::table('coupon_user')->insert([
                        'coupon_id' => $coupon->id,
                        'user_id' => auth()->id(),
                        'usage_count' => 1,
                    ]);
                }

            }

            



            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('items.product')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->find($id);
        return response()->json($order);
    }

    public function index()
    {
        $order = Order::with(['user','coupon','items.product'])->get();
        return response()->json($order);
    }

}

?>