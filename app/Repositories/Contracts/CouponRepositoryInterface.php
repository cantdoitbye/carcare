<?php

namespace App\Repositories\Contracts;

interface CouponRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByCode($code);
    public function getActive();
    public function getPublic();
    public function getExpired();
    public function getExpiringSoon($days = 7);
    public function getPopular($limit = 10);
    public function validateCoupon($code, $userId = null, $email = null);
    public function applyCoupon($couponId, $orderTotal, $cartItems = []);
    public function recordUsage($couponId, $orderData);
    public function getUsageStatistics($couponId);
    public function bulkUpdateStatus(array $ids, $status);
}