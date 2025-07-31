<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;
class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => 'Welcome new customers with 10% off their first order',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => 50.00,
                'maximum_discount' => 20.00,
                'usage_limit' => 100,
                'usage_limit_per_customer' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'status' => 'active',
                'is_public' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'CARCARE25',
                'name' => 'Car Care Special',
                'description' => '$25 off orders over $100',
                'type' => 'fixed',
                'value' => 25.00,
                'minimum_amount' => 100.00,
                'usage_limit' => 50,
                'starts_at' => now(),
                'expires_at' => now()->addWeeks(4),
                'status' => 'active',
                'is_public' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Free Shipping',
                'description' => 'Free shipping on all orders',
                'type' => 'fixed',
                'value' => 0.00,
                'free_shipping' => true,
                'minimum_amount' => 75.00,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(1),
                'status' => 'active',
                'is_public' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'VIPONLY',
                'name' => 'VIP Exclusive',
                'description' => 'Exclusive 15% discount for VIP members',
                'type' => 'percentage',
                'value' => 15.00,
                'maximum_discount' => 50.00,
                'usage_limit_per_customer' => 3,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'status' => 'active',
                'is_public' => false,
                'created_by' => 1,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::create($couponData);
        }

    }
}
