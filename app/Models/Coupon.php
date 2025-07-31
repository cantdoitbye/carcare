<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'starts_at',
        'expires_at',
        'applicable_products',
        'applicable_categories',
        'excluded_products',
        'excluded_categories',
        'free_shipping',
        'exclude_sale_items',
        'status',
        'is_public',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'applicable_products' => 'array',
            'applicable_categories' => 'array',
            'excluded_products' => 'array',
            'excluded_categories' => 'array',
            'free_shipping' => 'boolean',
            'exclude_sale_items' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    // Relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function applicableProducts()
    {
        if (!$this->applicable_products) {
            return collect();
        }
        return Product::whereIn('id', $this->applicable_products)->get();
    }

    public function applicableCategories()
    {
        if (!$this->applicable_categories) {
            return collect();
        }
        return Category::whereIn('id', $this->applicable_categories)->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    // Helper methods
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasUsageLimit(): bool
    {
        return $this->usage_limit !== null;
    }

    public function hasCustomerUsageLimit(): bool
    {
        return $this->usage_limit_per_customer !== null;
    }

    public function canBeUsedBy($userId = null, $email = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->hasCustomerUsageLimit()) {
            $usageCount = $this->usages()
                ->where(function ($query) use ($userId, $email) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } elseif ($email) {
                        $query->where('email', $email);
                    }
                })
                ->count();

            if ($usageCount >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($orderTotal, $cartItems = []): array
    {
        if (!$this->isValid()) {
            return ['amount' => 0, 'error' => 'Coupon is not valid'];
        }

        if ($this->minimum_amount && $orderTotal < $this->minimum_amount) {
            return [
                'amount' => 0, 
                'error' => "Minimum order amount of $" . number_format($this->minimum_amount, 2) . " required"
            ];
        }

        // Calculate applicable total (considering product/category restrictions)
        $applicableTotal = $this->getApplicableTotal($orderTotal, $cartItems);

        if ($applicableTotal <= 0) {
            return ['amount' => 0, 'error' => 'No applicable items in cart'];
        }

        $discountAmount = 0;

        if ($this->type === 'fixed') {
            $discountAmount = min($this->value, $applicableTotal);
        } elseif ($this->type === 'percentage') {
            $discountAmount = ($applicableTotal * $this->value) / 100;
            
            if ($this->maximum_discount) {
                $discountAmount = min($discountAmount, $this->maximum_discount);
            }
        }

        return [
            'amount' => round($discountAmount, 2),
            'applicable_total' => $applicableTotal,
            'free_shipping' => $this->free_shipping,
        ];
    }

    protected function getApplicableTotal($orderTotal, $cartItems = []): float
    {
        // If no restrictions, apply to full order
        if (empty($this->applicable_products) && empty($this->applicable_categories) && 
            empty($this->excluded_products) && empty($this->excluded_categories)) {
            return $orderTotal;
        }

        // If we don't have cart items, we can't calculate specific applicability
        if (empty($cartItems)) {
            return $orderTotal;
        }

        $applicableTotal = 0;

        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $categoryIds = $item['category_ids'] ?? [];
            $itemTotal = $item['total'] ?? 0;

            // Skip if product is excluded
            if ($this->excluded_products && in_array($productId, $this->excluded_products)) {
                continue;
            }

            // Skip if any category is excluded
            if ($this->excluded_categories && array_intersect($categoryIds, $this->excluded_categories)) {
                continue;
            }

            // Skip sale items if configured
            if ($this->exclude_sale_items && ($item['on_sale'] ?? false)) {
                continue;
            }

            // Include if no specific restrictions or if product/category matches
            $includeItem = true;

            if ($this->applicable_products || $this->applicable_categories) {
                $includeItem = false;

                if ($this->applicable_products && in_array($productId, $this->applicable_products)) {
                    $includeItem = true;
                }

                if ($this->applicable_categories && array_intersect($categoryIds, $this->applicable_categories)) {
                    $includeItem = true;
                }
            }

            if ($includeItem) {
                $applicableTotal += $itemTotal;
            }
        }

        return $applicableTotal;
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
        
        // Auto-expire if usage limit reached
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            $this->update(['status' => 'expired']);
        }
    }

    public function getRemainingUsesAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }

        return '$' . number_format($this->value, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => $this->isExpired() ? 'expired' : 'active',
            'inactive' => 'inactive',
            'expired' => 'expired',
            default => 'inactive'
        };
    }
}
