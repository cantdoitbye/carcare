<?php

// app/Http/Resources/CategoryResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'icon' => $this->icon,
            'parent_id' => $this->parent_id,
            'has_children' => $this->hasChildren(),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}