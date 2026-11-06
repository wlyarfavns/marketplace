<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminSpaTest extends TestCase
{
    /**
     * Test that the SPA script file exists and is accessible.
     */
    public function test_spa_engine_script_file_exists(): void
    {
        $this->assertFileExists(public_path('js/admin-spa.js'));
        $content = file_get_contents(public_path('js/admin-spa.js'));
        $this->assertStringContainsString('AdminSPA', $content);
        $this->assertStringContainsString('isExcluded', $content);
        $this->assertStringContainsString('applyContent', $content);
        $this->assertStringContainsString('updateSidebarActiveLink', $content);
    }

    /**
     * Test that admin layout contains all SPA persistent containers and markers.
     */
    public function test_admin_layout_contains_spa_persistent_elements(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        // Persistent containers
        $response->assertSee('id="admin-spa-progress"', false);
        $response->assertSee('id="admin-sidebar-nav"', false);
        $response->assertSee('id="admin-page-title"', false);
        $response->assertSee('id="admin-flash-container"', false);
        $response->assertSee('id="admin-main-content"', false);
        $response->assertSee('js/admin-spa.js', false);

        // Sidebar SPA Route Attributes
        $expectedRoutes = [
            'data-spa-route="dashboard"',
            'data-spa-route="orders"',
            'data-spa-route="sellers.report"',
            'data-spa-route="products"',
            'data-spa-route="brands"',
            'data-spa-route="categories"',
            'data-spa-route="sellers"',
            'data-spa-route="users"',
            'data-spa-route="coupons"',
            'data-spa-route="content.banners"',
            'data-spa-route="content.features"',
            'data-spa-route="content.customer-services"',
            'data-spa-route="content.banks"',
            'data-spa-route="content.socials"',
            'data-spa-route="content.handles"',
        ];

        foreach ($expectedRoutes as $routeAttr) {
            $response->assertSee($routeAttr, false);
        }
    }

    /**
     * Test that all admin modules return valid SPA containers when requested via X-SPA-Request.
     */
    public function test_all_16_admin_modules_serve_valid_spa_content(): void
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
            $response = $this->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-SPA-Request' => 'true',
            ])->get($url);

            $response->assertStatus(200);
            $response->assertSee('id="admin-main-content"', false);
            $response->assertSee('id="admin-page-title"', false);
        }
    }
}

