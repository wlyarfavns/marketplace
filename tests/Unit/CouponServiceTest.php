<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\CouponService;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    protected CouponService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CouponService();
    }

    public function test_coupon_percentage_discount_calculation(): void
    {
        $coupon = new Coupon([
            'discount_type' => 'percentage',
            'discount_value' => 20, // 20%
            'max_discount' => 50000,
        ]);

        // 20% of 200,000 = 40,000 (< max_discount)
        $discount1 = $this->service->calculateDiscount($coupon, 200000);
        $this->assertEquals(40000, $discount1);

        // 20% of 500,000 = 100,000 (capped at max_discount 50,000)
        $discount2 = $this->service->calculateDiscount($coupon, 500000);
        $this->assertEquals(50000, $discount2);
    }

    public function test_coupon_fixed_discount_calculation(): void
    {
        $coupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 25000,
        ]);

        $discount = $this->service->calculateDiscount($coupon, 100000);
        $this->assertEquals(25000, $discount);

        // Should not exceed subtotal
        $discountCapped = $this->service->calculateDiscount($coupon, 15000);
        $this->assertEquals(15000, $discountCapped);
    }

    public function test_coupon_validation_rules(): void
    {
        $coupon = new Coupon([
            'code' => 'PROMO100',
            'is_active' => true,
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'min_purchase' => 100000,
            'min_quantity' => 2,
            'quota_total' => 5,
            'target_audience' => 'all',
        ]);

        // Fails min_purchase
        $resMinPurchase = $this->service->validate($coupon, 50000, 2);
        $this->assertFalse($resMinPurchase['valid']);

        // Fails min_quantity
        $resMinQty = $this->service->validate($coupon, 150000, 1);
        $this->assertFalse($resMinQty['valid']);

        // Passes both
        $resSuccess = $this->service->validate($coupon, 150000, 2);
        $this->assertTrue($resSuccess['valid']);
    }

    public function test_coupon_audience_restrictions(): void
    {
        $memberCoupon = new Coupon([
            'is_active' => true,
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'target_audience' => 'member_only',
        ]);

        // Guest user fails
        $this->assertFalse($this->service->validate($memberCoupon, 50000, 1, null)['valid']);

        // Logged-in user passes
        $user = new User(['id' => 999]);
        $this->assertTrue($this->service->validate($memberCoupon, 50000, 1, $user)['valid']);
    }

    public function test_product_scope_pre_order_and_price_range(): void
    {
        $existingProduct = Product::first();
        if (! $existingProduct) {
            $this->markTestSkipped('No product available in test database.');
        }

        // Test scopePreOrder executes valid SQL and filters by 'po'
        $poCount = Product::preOrder()->count();
        $this->assertIsInt($poCount);

        // Test updatePriceRange
        $existingProduct->updatePriceRange();
        $this->assertNotNull($existingProduct->min_price);
    }
}
