<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Carbon\Carbon;

class CouponRepository implements CouponRepositoryInterface
{
    public function __construct(
        protected Coupon $model,
        protected CouponUsage $usageModel
    ) {}

    public function all()
    {
        return $this->model->with('createdBy')->latest()->get();
    }

    public function find($id)
    {
        return $this->model->with('createdBy', 'usages')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $coupon = $this->find($id);
        $coupon->update($data);
        return $coupon;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function findByCode($code)
    {
        return $this->model->where('code', strtoupper($code))->first();
    }

    public function getActive()
    {
        return $this->model->active()->with('createdBy')->latest()->get();
    }

    public function getPublic()
    {
        return $this->model->active()->public()->latest()->get();
    }

    public function getExpired()
    {
        return $this->model->where('status', 'expired')
            ->orWhere('expires_at', '<', now())
            ->with('createdBy')
            ->latest()
            ->get();
    }

    public function getExpiringSoon($days = 7)
    {
        return $this->model->active()
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays($days))
            ->with('createdBy')
            ->orderBy('expires_at')
            ->get();
    }

    public function getPopular($limit = 10)
    {
        return $this->model->active()
            ->orderBy('used_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function validateCoupon($code, $userId = null, $email = null)
    {
        $coupon = $this->findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon code not found'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'Coupon is not valid or has expired'];
        }

        if (!$coupon->canBeUsedBy($userId, $email)) {
            return ['valid' => false, 'message' => 'You have reached the usage limit for this coupon'];
        }

        return ['valid' => true, 'coupon' => $coupon];
    }

    public function applyCoupon($couponId, $orderTotal, $cartItems = [])
    {
        $coupon = $this->find($couponId);
        return $coupon->calculateDiscount($orderTotal, $cartItems);
    }

    public function recordUsage($couponId, $orderData)
    {
        $coupon = $this->find($couponId);
        
        $usage = $this->usageModel->create([
            'coupon_id' => $couponId,
            'user_id' => $orderData['user_id'] ?? null,
            'order_id' => $orderData['order_id'] ?? null,
            'email' => $orderData['email'] ?? null,
            'discount_amount' => $orderData['discount_amount'],
            'order_total' => $orderData['order_total'],
            'order_details' => $orderData['order_details'] ?? null,
            'used_at' => now(),
        ]);

        $coupon->incrementUsage();

        return $usage;
    }

    public function getUsageStatistics($couponId)
    {
        $coupon = $this->find($couponId);
        
        return [
            'total_uses' => $coupon->used_count,
            'remaining_uses' => $coupon->remaining_uses,
            'total_discount_given' => $coupon->usages()->sum('discount_amount'),
            'average_order_value' => $coupon->usages()->avg('order_total'),
            'unique_customers' => $coupon->usages()->distinct('user_id')->count('user_id'),
            'usage_by_day' => $coupon->usages()
                ->selectRaw('DATE(used_at) as date, COUNT(*) as count, SUM(discount_amount) as total_discount')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
        ];
    }

    public function bulkUpdateStatus(array $ids, $status)
    {
        return $this->model->whereIn('id', $ids)->update(['status' => $status]);
    }
}