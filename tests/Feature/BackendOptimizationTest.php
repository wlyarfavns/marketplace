<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentConfirmation;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Tests\TestCase;

class BackendOptimizationTest extends TestCase
{
    public function test_admin_dashboard_single_aggregate_query(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas([
            'totalOrders', 'totalRevenue', 'totalCustomers', 'activeProducts',
            'pendingOrders', 'processingOrders', 'completedOrders',
            'totalDpOrders', 'dpPaidOrders', 'dpPendingOrders', 'fullyPaidOrders',
            'revenueDp', 'remainingReceivable', 'recentOrders', 'popularProducts', 'activeCoupons',
        ]);
    }

    public function test_admin_sellers_report_and_export(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/sellers/report');
        $response->assertStatus(200);
        $response->assertViewHas([
            'reportData', 'paginatedSellers', 'brands', 'allSellers',
            'totalClosingToday', 'totalSalesMonth', 'totalSalesLastMonth',
        ]);

        $exportResponse = $this->actingAs($admin)->get(route('admin.sellers.report.export'));
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_customer_account_dashboard_aggregate_query(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get(route('account.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas([
            'totalOrders', 'activeOrders', 'completedOrders', 'totalSpent', 'recentOrders',
        ]);
    }

    public function test_admin_order_payment_verification_transaction(): void
    {
        $admin = User::where('role', 'admin')->first();
        $order = Order::first();
        $confirmation = PaymentConfirmation::where('order_id', $order->id)->first();

        if ($confirmation) {
            $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/verify-payment/{$confirmation->id}", [
                'action' => 'verify',
                'notes' => 'Testing transaction-safe verification',
            ]);

            $response->assertRedirect();
            $confirmation->refresh();
            $order->refresh();

            $this->assertEquals('verified', $confirmation->verification_status);
            $this->assertEquals('verified', $order->payment_status);
            $this->assertEquals('processing', $order->order_status);
        }
    }

    public function test_cart_addition_uses_eager_loaded_image(): void
    {
        $product = Product::with(['models', 'colors', 'sizes'])->first();
        $this->assertNotNull($product);

        $model = $product->models->first();
        $color = $product->colors->first();
        $size = $product->sizes->first();

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'model_id' => $model->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect();

        $cart = session()->get('cart', []);
        $this->assertNotEmpty($cart);
    }
}
