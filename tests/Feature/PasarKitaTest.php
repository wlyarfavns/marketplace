<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_product_detail_contains_colorway_images(): void
    {
        $multicolorProducts = [
            'asics-gel-kayano-14-metallic-silver-black' => ['asics-gel-kayano-white.jpg', 'Metallic Silver & Black', 'Pure White & Smoke Grey'],
            'nike-dunk-low-retro-panda' => ['nike-dunk-white.jpg', 'White & Black (Panda)', 'Triple White'],
            'nike-metcon-9-training-gym' => ['nike-metcon-white.jpg', 'Black & White Anthracite', 'Triple White Metallic'],
            'nike-air-zoom-pegasus-40-runner' => ['nike-pegasus-white.jpg', 'Pure Platinum Black', 'Triple White'],
            'nike-calm-slide-triple-black' => ['nike-calm-white.jpg', 'Triple Black', 'Pure Sail White'],
            'adidas-samba-og-cloud-white-core-black' => ['adidas-samba-black.jpg', 'Cloud White & Core Black', 'Core Black & White'],
            'adidas-adilette-22-comfort-slides' => ['adidas-adilette-black.jpg', 'Stardust White Charcoal', 'Carbon Black'],
            'new-balance-550-white-sea-salt-black' => ['nb-550-grey.jpg', 'White & Core Black', 'Sea Salt & Monochrome Grey'],
            'puma-palermo-classic-black-gum-white' => ['puma-palermo-white.jpg', 'Core Black & White', 'Puma White & Black'],
            'converse-chuck-70-vintage-high-black-egret' => ['converse-chuck-white.jpg', 'Black Canvas & Egret', 'Optic White Canvas'],
            'vans-old-skool-classic-black-white' => ['vans-oldskool-white.jpg', 'Core Black & White Sidestripe', 'Triple White Canvas'],
        ];

        foreach ($multicolorProducts as $slug => [$imageFilename, $color1, $color2]) {
            $product = Product::where('slug', $slug)->first();
            if ($product) {
                $response = $this->get('/produk/'.$product->slug);
                $response->assertStatus(200);
                $response->assertSee($imageFilename);
                $response->assertSee($color1);
                $response->assertSee($color2);
                $this->assertFileExists(public_path('images/shoes/'.$imageFilename));
            }
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

    // 10. Pengujian Alur Checkout Lengkap (Guest / Customer Checkout)
    public function test_full_checkout_process_creates_order(): void
    {
        $product = Product::with(['models', 'colors', 'sizes', 'brand'])->first();
        $model = $product->models->first();
        $color = $product->colors->first();
        $size = $product->sizes->first();

        $cartKey = "{$product->id}_{$model->id}_{$color->id}_{$size->id}";
        $cartData = [
            $cartKey => [
                'cart_key' => $cartKey,
                'product_id' => $product->id,
                'brand_id' => $product->brand_id,
                'brand_name' => $product->brand ? $product->brand->name : 'Nike',
                'dp_percentage' => 30.0,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_type' => $product->product_type,
                'weight_grams' => $product->weight_grams,
                'model_name' => $model->model_name,
                'color_name' => $color->color_name,
                'color_hex' => $color->hex_code,
                'size_name' => $size->size_label,
                'unit_price' => (float) $product->min_price,
                'rule_adjustment' => 0,
                'quantity' => 1,
                'subtotal' => (float) $product->min_price,
                'bonus_item' => null,
                'image' => null,
            ],
        ];

        $checkoutPayload = [
            'recipient_first_name' => 'Willy',
            'recipient_last_name' => 'Avines',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Merdeka No. 45',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12190',
            'courier_name' => 'JNE Express',
            'payment_method_type' => 'full',
            'customer_notes' => 'Tolong bubble wrap ganda.',
        ];

        $response = $this->withSession(['cart' => $cartData])
            ->post(route('checkout.process'), $checkoutPayload);

        $order = Order::where('recipient_first_name', 'Willy')
            ->where('whatsapp', '081234567890')
            ->latest()
            ->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.success', $order->order_number));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'recipient_first_name' => 'Willy',
            'payment_method_type' => 'full',
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }

    // 11. Pengujian Unggah Bukti Pembayaran
    public function test_payment_proof_upload_and_confirmation(): void
    {
        Storage::fake('public');
        $order = Order::latest()->first();
        $bank = BankAccount::first();

        $fakeProof = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->post(route('checkout.upload-proof', $order->order_number), [
            'bank_account_id' => $bank ? $bank->id : null,
            'payment_stage' => 'full',
            'amount' => $order->grand_total,
            'proof_file' => $fakeProof,
            'notes' => 'Transfer via mobile banking BCA.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_confirmations', [
            'order_id' => $order->id,
            'payment_stage' => 'full',
            'verification_status' => 'pending',
        ]);

        $this->assertEquals('waiting_verification', $order->fresh()->payment_status);
    }

    // 12. Pengujian Verifikasi Pembayaran & Pembaruan Status oleh Admin
    public function test_admin_payment_verification_and_order_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $order = Order::has('paymentConfirmations')->latest()->first();
        $confirmation = $order->paymentConfirmations->first();

        // Verifikasi Pembayaran
        $response = $this->post(route('admin.orders.verify-payment', ['orderId' => $order->id, 'confirmationId' => $confirmation->id]), [
            'action' => 'verify',
            'notes' => 'Dana telah masuk ke rekening.',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('verified', $confirmation->fresh()->verification_status);
        $this->assertEquals('verified', $order->fresh()->payment_status);
        $this->assertEquals('processing', $order->fresh()->order_status);

        // Update Status Pesanan ke Shipped
        $statusResponse = $this->put(route('admin.orders.update-status', $order->id), [
            'order_status' => 'shipped',
            'payment_status' => 'verified',
            'dp_status' => 'fully_paid',
        ]);

        $statusResponse->assertSessionHas('success');
        $this->assertEquals('shipped', $order->fresh()->order_status);
    }

    // 13. Pengujian Cetak Faktur PDF
    public function test_admin_invoice_pdf_download(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $order = Order::latest()->first();
        $response = $this->get(route('admin.orders.invoice-pdf', $order->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    // 14. Pengujian Ekspor Data Pesanan & Laporan Mitra (Format Excel .XLSX Resmi & Rapi)
    public function test_admin_csv_reports_export(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $orderExcel = $this->get(route('admin.orders.export-excel'));
        $orderExcel->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $orderExcel->headers->get('content-type'));

        $sellerExcel = $this->get(route('admin.sellers.report.export'));
        $sellerExcel->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $sellerExcel->headers->get('content-type'));
    }

    // 15. Pengujian Form Edit & Update Produk Admin
    public function test_admin_product_edit_and_update(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $brand = Brand::first();
        $category = Category::first();
        $testSku = 'TEST-SKU-'.uniqid();
        $product = Product::create([
            'name' => 'Sneaker Test Dedicated',
            'slug' => 'sneaker-test-'.uniqid(),
            'brand_id' => $brand->id,
            'sku' => $testSku,
            'product_type' => 'ready_stock',
            'weight_grams' => 800,
            'status' => 'active',
            'min_price' => 1500000,
            'max_price' => 1500000,
        ]);
        $product->categories()->sync([$category->id]);

        // Buka Halaman Edit Produk
        $editResponse = $this->get(route('admin.products.edit', $product->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee($product->name);

        // Simpan Perubahan Produk
        $updatePayload = [
            'name' => 'Sneaker Test Dedicated Updated',
            'brand_id' => $product->brand_id,
            'sku' => $testSku.'-REV',
            'product_type' => 'ready_stock',
            'weight_grams' => 950,
            'status' => 'active',
            'categories' => [$category->id],
            'base_price' => 2200000,
            'description' => 'Siluet ikonik diperbarui dengan material premium.',
            'colors' => [
                ['name' => 'Triple Black', 'hex' => '#000000', 'color_image_path' => 'images/shoes/black.jpg'],
                ['name' => 'Triple White', 'hex' => '#ffffff', 'color_image_path' => 'images/shoes/white.jpg'],
            ],
            'models' => [
                ['name' => 'Standard OG Box', 'description' => 'Original Packaging'],
            ],
            'sizes' => [
                ['label' => 'EU 42 (US 8.5)'],
            ],
            'matrix_prices' => [
                ['model' => 'Standard OG Box', 'size' => 'EU 42 (US 8.5)', 'price' => 2200000, 'stock' => 20],
            ],
        ];

        $updateResponse = $this->put(route('admin.products.update', $product->id), $updatePayload);
        $updateResponse->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Sneaker Test Dedicated Updated',
            'min_price' => 2200000,
        ]);
    }
}
