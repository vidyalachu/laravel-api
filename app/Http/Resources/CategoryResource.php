<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request); // return everting

        return[
            'id'        =>  $this->id,
            'name'      =>  $this->name,
            'slug'      =>  $this->slug,
            'status'    =>  $this->status,
            'image'     =>  $this->image,

            'is_active' => $this->status === 'active',
            // relationship (only if loaded)
            'products' => ProductResource::collection($this->whenLoaded('products')), 
        ];
    }
}
