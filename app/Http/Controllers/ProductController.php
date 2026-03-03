<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update(
                        $product,
                        $request->validated()
                    );


        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function updateStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,draft,archived'
        ]);

        $product->update($request->only(['status']));

        return response()->json([
            'message' => 'Product Status updated successfully',
            'product' => $product
        ]);
    }
    

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data'    => $product,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $filters = $request->only(['include']);
        $product = $this->productService->show($filters, $id);   
        return new ProductResource($product);
    }


    public function index(Request $request)
    {
        $filters = $request->only([
                        'status',
                        'min_price',
                        'max_price',
                        'sort',
                        'include'
                    ]);        
        $products = $this->productService->getAll($filters);
        return ProductResource::collection($products);
    }
}
