<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Tests\TestCase;

class PasarKitaTest extends TestCase
{
    // 1. Pengujian Akses Front-Store
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('FOOTWORK');
    }

    public function test_catalog_loads_successfully(): void
    {
        $response = $this->get('/katalog');
        $response->assertStatus(200);
    }

    public function test_product_detail_loads_successfully(): void
    {
        $product = Product::where('status', 'active')->first();
        if ($product) {
            $response = $this->get('/produk/'.$product->slug);
            $response->assertStatus(200);
            $response->assertSee($product->name);
        }
    }

    public function test_cart_page_loads(): void
    {
        $response = $this->get('/keranjang');
        $response->assertStatus(200);
    }

    // 2. Pengujian Produk Pre-Order (PO)
    public function test_po_product_loads_successfully(): void
    {
        $poProduct = Product::where('product_type', 'po')->first();
        if ($poProduct) {
            $response = $this->get('/produk/'.$poProduct->slug);
            $response->assertStatus(200);
            $response->assertSee($poProduct->name);
        }
    }

    // 3. Pengujian Role Pelanggan (Customer)
    public function test_pelanggan_can_access_account_dashboard(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $dashboardResponse = $this->get('/akun/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Akun Saya');
    }

    // 5. Pengujian Role Administrator (Panel Back-Office)
    public function test_admin_can_access_all_16_modules(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $adminUrls = [
            '/admin/dashboard',
            '/admin/orders',
            '/admin/sellers/report',
            '/admin/products',
            '/admin/products/create',
            '/admin/brands',
            '/admin/categories',
            '/admin/sellers',
            '/admin/users',
            '/admin/coupons',
            '/admin/content/banners',
            '/admin/content/features',
            '/admin/content/customer-services',
            '/admin/content/banks',
            '/admin/content/socials',
            '/admin/content/handles',
        ];

        foreach ($adminUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }

    // 6. Non-admin Dilarang Mengakses Panel Admin
    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $member = User::where('role', 'customer')->first();
        $this->actingAs($member);

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    // 7. Pengujian AJAX Cek ID Seller
    public function test_ajax_check_seller(): void
    {
        $seller = Seller::where('is_active', true)->first();
        $response = $this->postJson('/ajax/check-seller', [
            'seller_code' => $seller->seller_code,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'id' => $seller->id,
        ]);
    }

    // 8. Pengujian AJAX Cek Kupon Diskon
    public function test_ajax_check_coupon(): void
    {
        $coupon = Coupon::where('is_active', true)->where('target_audience', 'all')->first();
        if ($coupon) {
            $response = $this->postJson('/ajax/check-coupon', [
                'code' => $coupon->code,
                'subtotal' => 200000,
                'total_quantity' => 2,
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'valid' => true,
            ]);
        }
    }

    // 9. Pengujian Gambar dan Kelengkapan Katalog Sepatu
    public function test_shoe_products_and_images_are_available(): void
    {
        $this->assertDatabaseHas('brands', ['slug' => 'nike']);
        $this->assertDatabaseHas('brands', ['slug' => 'air-jordan']);
        $this->assertDatabaseHas('brands', ['slug' => 'adidas']);
        $this->assertDatabaseHas('brands', ['slug' => 'new-balance']);

        $visibleProduct = Product::active()
            ->where('visible_to_umum', true)
            ->where('product_type', 'ready_stock')
            ->has('images')
            ->latest()
            ->first();

        $this->assertNotNull($visibleProduct);
        $this->assertNotNull($visibleProduct->primary_image_url);

        $response = $this->get('/katalog');
        $response->assertStatus(200);
        $response->assertSee(e($visibleProduct->name), false);
        $response->assertSee($visibleProduct->primary_image_url);
    }
}
