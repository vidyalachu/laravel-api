<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|unique:categories,name,' . $id,
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        // Generate slug if name changed
        $slug = $category->slug;
        if ($category->name !== $request->name) {
            $slug = Str::slug($request->name);
        }

        $imagePath = $category->image;

        // If new image uploaded
        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name'   => $request->name,
            'slug'   => $slug,
            'image'  => $imagePath,
            'status' => $request->status
        ]);

        return response()->json([
            'message'  => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    public function index(Request $request){
        $query = Category::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id)
    {
        $query = Category::query();

        if ($request->include === 'products') {
            $query->with('products');
        }

        $category = $query->findOrFail($id);

        return new CategoryResource($category);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|unique:categories,name',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        // Generate slug automatically
        $slug = Str::slug($request->name);

        // Handle Image Upload
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name'   => $request->name,
            'slug'   => $slug,
            'image'  => $imagePath,
            'status' => $request->status
        ]);

        return response()->json([
            'message'  => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    
}
