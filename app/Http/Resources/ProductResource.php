<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'              => $this->name,
            'description'       => $this->description,
            'price'             => $this->price,
            'formatted_price'   => '₹' . number_format($this->price, 2),
            'stock'             => $this->stock,
            'status'            => $this->status,

            'image_url'         => $this->image 
                                    ? asset('storage/' . $this->image) 
                                    : null,

            'is_active'         => $this->status === 'active',
            'in_stock'          => $this->stock > 0,

            'updated_date'      => $this->updated_at?->format('d-m-Y'),
            'created_date'      => $this->created_at?->format('d-m-Y'),

            'category'          => CategoryResource::make(
                                    $this->whenLoaded('category')
                                ),
        ];
    }
}
