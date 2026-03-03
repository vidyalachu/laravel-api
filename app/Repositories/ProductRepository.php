<?php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    protected array $allowedIncludes = ['category'];

    public function getAll(array $filters)
    {
        $query = Product::query()
            ->filter($filters)
            ->sort($filters['sort'] ?? null);

        $this->applyIncludes($query, $filters);

        return $query->paginate(10);
    }

    public function findById(int $id, array $filters = [])
    {
        $query = Product::query();

        $this->applyIncludes($query, $filters);

        return $query->findOrFail($id);
    }

    private function applyIncludes($query, array $filters): void
    {
        if (!empty($filters['include'])) {

            $includes = explode(',', $filters['include']);

            $validIncludes = array_intersect(
                $includes,
                $this->allowedIncludes
            );

            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }
    }
}