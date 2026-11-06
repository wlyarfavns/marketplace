<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class FrontSpaTest extends TestCase
{
    /**
     * Test that the Storefront SPA engine script file exists and is well-formed.
     */
    public function test_front_spa_script_file_exists(): void
    {
        $this->assertFileExists(public_path('js/front-spa.js'));
        $content = file_get_contents(public_path('js/front-spa.js'));
        $this->assertStringContainsString('FrontSPA', $content);
        $this->assertStringContainsString('setupProgressBar', $content);
        $this->assertStringContainsString('setupFormSubmitOverride', $content);
        $this->assertStringContainsString('isExcluded', $content);
        $this->assertStringContainsString('updateActiveNav', $content);
        $this->assertStringContainsString('applyContent', $content);
        $this->assertStringContainsString('executeScriptsAndInitAlpine', $content);
        $this->assertStringContainsString('front:cart-sync', $content);
    }

    /**
     * Test that storefront layout contains all SPA persistent containers and markers.
     */
    public function test_storefront_layout_contains_spa_persistent_elements(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Persistent containers
        $response->assertSee('id="front-spa-progress"', false);
        $response->assertSee('id="front-nav-links"', false);
        $response->assertSee('id="front-header-search"', false);
        $response->assertSee('id="front-header-search-input"', false);
        $response->assertSee('id="front-cart-count"', false);
        $response->assertSee('id="front-flash-container"', false);
        $response->assertSee('id="front-main-content"', false);
        $response->assertSee('id="front-user-bar"', false);
        $response->assertSee('js/front-spa.js', false);

        // Nav Route Attributes
        $expectedNavRoutes = [
            'data-front-route="home"',
            'data-front-route="ready_stock"',
            'data-front-route="po"',
            'data-front-route="catalog"',
        ];

        foreach ($expectedNavRoutes as $routeAttr) {
            $response->assertSee($routeAttr, false);
        }
    }

    /**
     * Test that all public storefront pages return valid SPA content container.
     */
    public function test_all_public_storefront_pages_serve_valid_spa_content(): void
    {
        $urls = [
            '/',
            '/katalog',
            '/katalog?type=ready_stock',
            '/katalog?type=po',
            '/katalog?q=nike',
            '/keranjang',
            '/login',
            '/register',
        ];

        foreach ($urls as $url) {
            $response = $this->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-SPA-Request' => 'true',
            ])->get($url);

            $response->assertStatus(200);
            $response->assertSee('id="front-main-content"', false);
            $response->assertSee('id="front-flash-container"', false);
        }
    }

    /**
     * Test that product detail page renders SPA container and required Alpine initialization scripts.
     */
    public function test_product_detail_serves_valid_spa_content_with_alpine_scripts(): void
    {
        $product = Product::first();
        if (! $product) {
            $this->markTestSkipped('No product available to test detail page.');
        }

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'X-SPA-Request' => 'true',
        ])->get('/produk/'.$product->slug);

        $response->assertStatus(200);
        $response->assertSee('id="front-main-content"', false);
        $response->assertSee('window.productPageConfig', false);
        $response->assertSee('productDetailState', false);
    }

    /**
     * Test that checkout page renders SPA container with session cart.
     */
    public function test_checkout_serves_valid_spa_content_with_cart(): void
    {
        $product = Product::first();
        if (! $product) {
            $this->markTestSkipped('No product available for cart checkout test.');
        }

        // Put dummy cart in session
        $cartKey = 'test_item_1';
        $cart = [
            $cartKey => [
                'cart_key' => $cartKey,
                'product_id' => $product->id,
                'brand_id' => $product->brand_id,
                'brand_name' => 'Nike',
                'dp_percentage' => 30.0,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_type' => 'ready_stock',
                'weight_grams' => 1000,
                'model_name' => 'Standard',
                'color_name' => 'White',
                'color_hex' => '#FFFFFF',
                'size_name' => '42',
                'unit_price' => 1000000,
                'rule_adjustment' => 0,
                'quantity' => 1,
                'subtotal' => 1000000,
                'bonus_item' => null,
                'image' => null,
            ],
        ];

        $response = $this->withSession(['cart' => $cart])
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-SPA-Request' => 'true',
            ])->get('/checkout');

        $response->assertStatus(200);
        $response->assertSee('id="front-main-content"', false);
        $response->assertSee('checkoutState', false);
    }

    /**
     * Test that customer account pages serve valid SPA content for authenticated customers.
     */
    public function test_customer_account_pages_serve_valid_spa_content(): void
    {
        $customer = User::where('role', 'customer')->first() ?? User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $accountUrls = [
            '/akun/dashboard',
            '/akun/pesanan',
            '/akun/profil',
        ];

        foreach ($accountUrls as $url) {
            $response = $this->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-SPA-Request' => 'true',
            ])->get($url);

            $response->assertStatus(200);
            $response->assertSee('id="front-main-content"', false);
            $response->assertSee('id="front-user-bar"', false);
        }
    }

    /**
     * Test that Admin routes remain isolated with admin-specific SPA containers.
     */
    public function test_admin_routes_remain_isolated_from_front_spa(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'X-SPA-Request' => 'true',
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
        // Must contain Admin SPA main content and NOT front SPA main content
        $response->assertSee('id="admin-main-content"', false);
        $response->assertDontSee('id="front-main-content"', false);
    }
}

