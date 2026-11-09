<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentConfirmation;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountOrderAndTransactionTest extends TestCase
{
    use DatabaseTransactions;

    private function createCustomerUser(?string $email = null): User
    {
        return User::create([
            'name' => 'Customer Tester',
            'email' => $email ?: 'customer_'.time().'_'.rand(1000, 9999).'@pasarkita.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    private function createOrderForUser(User $user, array $attributes = []): Order
    {
        $orderNumber = $attributes['order_number'] ?? 'ORD'.date('ymdHis').rand(1000, 9999);

        $order = Order::create(array_merge([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'recipient_first_name' => 'Budi',
            'recipient_last_name' => 'Santoso',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Sudirman No. 12',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
            'courier_name' => 'JNE Express',
            'shipping_cost' => 25000,
            'subtotal' => 1500000,
            'discount' => 0,
            'grand_total' => 1525000,
            'payment_method_type' => 'full',
            'dp_amount' => 0,
            'remaining_amount' => 0,
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
            'dp_status' => 'fully_paid',
        ], $attributes));

        $product = Product::first();
        if (! $product) {
            $brand = Brand::firstOrCreate(['name' => 'Nike'], ['slug' => 'nike']);
            $product = Product::create([
                'name' => 'Nike Air Jordan 1',
                'slug' => 'nike-air-jordan-1-'.time().'-'.rand(100, 999),
                'brand_id' => $brand->id,
                'product_type' => 'ready_stock',
                'status' => 'active',
            ]);
        }

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => 'Nike Air Jordan 1 Retro High',
            'model_name' => 'High OG',
            'color_name' => 'Chicago',
            'size_name' => '42',
            'unit_price' => 1500000,
            'rule_adjustment' => 0,
            'quantity' => 1,
            'subtotal' => 1500000,
            'bonus_item_snapshot' => 'Socks Exclusive',
        ]);

        return $order;
    }

    private function createPaymentConfirmationForOrder(Order $order, array $attributes = []): PaymentConfirmation
    {
        $bank = BankAccount::first();

        return PaymentConfirmation::create(array_merge([
            'order_id' => $order->id,
            'bank_account_id' => $bank ? $bank->id : null,
            'proof_file_path' => 'payment_proofs/sample.jpg',
            'payment_stage' => 'full',
            'amount' => $order->grand_total,
            'verification_status' => 'pending',
            'notes' => 'Transfer via mobile banking',
        ], $attributes));
    }

    public function test_guest_cannot_access_orders_or_transactions_and_is_redirected_to_login(): void
    {
        $this->get(route('account.orders'))->assertRedirect(route('login'));
        $this->get(route('account.transactions'))->assertRedirect(route('login'));
        $this->get(route('account.dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_orders_page_with_empty_state(): void
    {
        $user = $this->createCustomerUser();

        $response = $this->actingAs($user)->get(route('account.orders'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Riwayat Pesanan');
        $response->assertSee('Tidak Ada Pesanan Ditemukan');
        $response->assertSee('Mulai Belanja');
    }

    public function test_authenticated_user_can_view_orders_list_with_snapshots_and_products(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user, [
            'order_number' => 'ORD-SNAP-'.time(),
            'order_status' => 'processing',
        ]);

        $response = $this->actingAs($user)->get(route('account.orders'));
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('Nike Air Jordan 1 Retro High');
        $response->assertSee('Sedang Diproses');
        $response->assertSee('Rp 1.525.000');
        $response->assertSee('Chicago');
    }

    public function test_orders_can_be_filtered_by_status_and_search(): void
    {
        $user = $this->createCustomerUser();
        $uniqueKey = 'SRCH'.time();
        $order1 = $this->createOrderForUser($user, [
            'order_number' => 'ORD-'.$uniqueKey.'-1',
            'order_status' => 'processing',
        ]);
        $order2 = $this->createOrderForUser($user, [
            'order_number' => 'ORD-OTHER-'.time(),
            'order_status' => 'completed',
        ]);

        // Filter by Search
        $searchResponse = $this->actingAs($user)->get(route('account.orders', ['search' => $uniqueKey]));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($order1->order_number);
        $searchResponse->assertDontSee($order2->order_number);

        // Filter by Status
        $statusResponse = $this->actingAs($user)->get(route('account.orders', ['status' => 'completed']));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee($order2->order_number);
        $statusResponse->assertDontSee($order1->order_number);
    }

    public function test_orders_can_be_filtered_by_date_range_and_sorted(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user);
        $order->created_at = Carbon::now()->subDays(5);
        $order->saveQuietly();

        $responseInRange = $this->actingAs($user)->get(route('account.orders', [
            'date_from' => Carbon::now()->subDays(6)->toDateString(),
            'date_to' => Carbon::now()->subDays(4)->toDateString(),
        ]));
        $responseInRange->assertStatus(200);
        $responseInRange->assertSee($order->order_number);

        $responseOutOfRange = $this->actingAs($user)->get(route('account.orders', [
            'date_from' => Carbon::now()->subDays(2)->toDateString(),
            'date_to' => Carbon::now()->toDateString(),
        ]));
        $responseOutOfRange->assertStatus(200);
        $responseOutOfRange->assertDontSee($order->order_number);
    }

    public function test_user_can_view_their_own_order_detail(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user, [
            'recipient_first_name' => 'Aditya',
            'recipient_last_name' => 'Pratama',
        ]);

        $response = $this->actingAs($user)->get(route('account.order.detail', $order->order_number));
        $response->assertStatus(200);
        $response->assertSee('Detail Pesanan');
        $response->assertSee($order->order_number);
        $response->assertSee('Aditya Pratama');
        $response->assertSee('Nike Air Jordan 1 Retro High');
    }

    public function test_user_cannot_view_another_users_order_detail_idor_protection(): void
    {
        $userA = $this->createCustomerUser();
        $userB = $this->createCustomerUser();

        $orderB = $this->createOrderForUser($userB);

        // User A attempts to view User B's order
        $response = $this->actingAs($userA)->get(route('account.order.detail', $orderB->order_number));
        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_view_transactions_page_with_empty_state(): void
    {
        $user = $this->createCustomerUser();

        $response = $this->actingAs($user)->get(route('account.transactions'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Transaksi Pembayaran');
        $response->assertSee('Tidak Ada Transaksi Pembayaran');
    }

    public function test_authenticated_user_can_view_transactions_list_with_bank_and_stage(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user, [
            'payment_method_type' => 'dp',
            'grand_total' => 2000000,
        ]);

        $trx = $this->createPaymentConfirmationForOrder($order, [
            'payment_stage' => 'dp',
            'amount' => 600000,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('account.transactions'));
        $response->assertStatus(200);
        $response->assertSee($trx->transaction_number);
        $response->assertSee($order->order_number);
        $response->assertSee('Uang Muka (DP)');
        $response->assertSee('Rp 600.000');
        $response->assertSee('Menunggu Verifikasi');
    }

    public function test_transactions_can_be_filtered_by_status_stage_and_search(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user);

        $trx1 = $this->createPaymentConfirmationForOrder($order, [
            'payment_stage' => 'dp',
            'amount' => 500000,
            'verification_status' => 'verified',
        ]);

        $trx2 = $this->createPaymentConfirmationForOrder($order, [
            'payment_stage' => 'remaining',
            'amount' => 1000000,
            'verification_status' => 'pending',
        ]);

        // Filter by stage = remaining
        $stageResponse = $this->actingAs($user)->get(route('account.transactions', ['stage' => 'remaining']));
        $stageResponse->assertStatus(200);
        $stageResponse->assertSee($trx2->transaction_number);
        $stageResponse->assertDontSee($trx1->transaction_number);

        // Filter by status = verified
        $statusResponse = $this->actingAs($user)->get(route('account.transactions', ['status' => 'verified']));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee($trx1->transaction_number);
        $statusResponse->assertDontSee($trx2->transaction_number);
    }

    public function test_user_can_view_their_own_transaction_detail(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user);

        $trx = $this->createPaymentConfirmationForOrder($order, [
            'amount' => 750000,
            'notes' => 'Catatan transfer pembayaran khusus unik '.time(),
        ]);

        $response = $this->actingAs($user)->get(route('account.transaction.detail', $trx->id));
        $response->assertStatus(200);
        $response->assertSee('Rincian Transaksi');
        $response->assertSee($trx->transaction_number);
        $response->assertSee($order->order_number);
        $response->assertSee($trx->notes);
    }

    public function test_user_cannot_view_another_users_transaction_detail_idor_protection(): void
    {
        $userA = $this->createCustomerUser();
        $userB = $this->createCustomerUser();

        $orderB = $this->createOrderForUser($userB);
        $trxB = $this->createPaymentConfirmationForOrder($orderB);

        // User A attempts to view User B's transaction
        $response = $this->actingAs($userA)->get(route('account.transaction.detail', $trxB->id));
        $response->assertStatus(404);
    }

    public function test_dashboard_displays_consolidated_stats_and_recent_transactions(): void
    {
        $user = $this->createCustomerUser();
        $order = $this->createOrderForUser($user, [
            'order_status' => 'processing',
        ]);
        $trx = $this->createPaymentConfirmationForOrder($order, [
            'amount' => 1250000,
        ]);

        $response = $this->actingAs($user)->get(route('account.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dasbor Pelanggan');
        $response->assertSee($order->order_number);
        $response->assertSee($trx->transaction_number);
        $response->assertSee('Total Transaksi');
    }
}
