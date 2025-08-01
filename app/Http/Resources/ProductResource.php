<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'price' => [
                'regular' => (float) $this->price,
                'compare' => $this->compare_price ? (float) $this->compare_price : null,
                'discount_percentage' => (float) $this->discount_percentage,
                'you_save' => $this->compare_price ? (float) ($this->compare_price - $this->price) : 0,
            ],
            'inventory' => [
                'stock_status' => $this->stock_status,
                'quantity' => $this->quantity,
                'track_inventory' => $this->track_inventory,
                'in_stock' => $this->stock_status === 'in_stock' && $this->quantity > 0,
            ],
            'attributes' => [
                'weight' => $this->weight ? (float) $this->weight : null,
                'dimensions' => [
                    'length' => $this->length ? (float) $this->length : null,
                    'width' => $this->width ? (float) $this->width : null,
                    'height' => $this->height ? (float) $this->height : null,
                ],
                'is_featured' => $this->is_featured,
                'is_digital' => $this->is_digital,
            ],
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image' => $this->getFirstImageUrl(),
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
            ],
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getFirstImageUrl()
    {
        if (!$this->relationLoaded('images')) {
            return null;
        }

        $primaryImage = $this->images->where('is_primary', true)->first();
        if ($primaryImage) {
            return asset('storage/' . $primaryImage->image_path);
        }

        $firstImage = $this->images->first();
        return $firstImage ? asset('storage/' . $firstImage->image_path) : null;
    }
}