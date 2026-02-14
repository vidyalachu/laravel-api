<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock'=>$request->stock
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ]);
    }

    public function index()
    {
        return Product::all();
    }
}
