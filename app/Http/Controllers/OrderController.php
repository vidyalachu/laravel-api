<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $total = 0;

            // Calculate total
            foreach ($request->items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(), // or $request->user_id
                'total_amount' => $total,
                'status' => 'pending'
            ]);

            // Insert order items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('items')
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

}

?>