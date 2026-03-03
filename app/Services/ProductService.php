<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    protected array $allowedIncludes = ['category'];
    public function getAll(array $filters)
    {
        $query = Product::query();

        $query = Product::query()
            ->filter($filters)
            ->sort($filters['sort'] ?? null);
        
        //condition include
        $this->applyIncludes($query, $filters);
        return $query->get();
    }

    public function show(array $filters, $id)
    {
        $query = Product::query();
        //condition include
        $this->applyIncludes($query, $filters);

        return $query->findOrFail($id);
    }
    private function applyIncludes($query, array $filters):void
    {
        if (!empty($filter['include'])) {
            $includes = explode(',',$filters['include']);
            $validIncludes  = array_intersect($includes,$allowedIncludes );
            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }
    }
    public function store(array $data): Product
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        $data['slug'] = Str::slug($data['name']);

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['image'])) {

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $data['image']->store('products', 'public');
        }
        // Regenerate slug if name changed
        if (isset($data['name']) && $product->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        return $product;
    }
}

?>