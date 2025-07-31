<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'brand',
        'price',
        'compare_price',
        'cost_price',
        'discount_percentage',
        'quantity',
        'min_quantity',
        'track_inventory',
        'stock_status',
        'weight',
        'length',
        'width',
        'height',
        'status',
        'is_featured',
        'is_digital',
        'meta_title',
        'meta_description',
        'tags',
        'attributes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'weight' => 'decimal:3',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'track_inventory' => 'boolean',
            'tags' => 'array',
            'attributes' => 'array',
        ];
    }

    // Relationships
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInStock(): bool
    {
        return $this->stock_status === 'in_stock' && $this->quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_percentage > 0 || ($this->compare_price && $this->compare_price > $this->price);
    }

    public function getDiscountAmountAttribute(): float
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return $this->compare_price - $this->price;
        }
        return ($this->price * $this->discount_percentage) / 100;
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->price - $this->getDiscountAmountAttribute();
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primaryImage = $this->primaryImage()->first();
        return $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;
    }

    // Update stock status based on quantity
    public function updateStockStatus(): void
    {
        if ($this->quantity <= 0) {
            $this->stock_status = 'out_of_stock';
        } elseif ($this->quantity <= $this->min_quantity) {
            $this->stock_status = 'low_stock';
        } else {
            $this->stock_status = 'in_stock';
        }
        $this->save();
    }
}
