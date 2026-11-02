<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes PasarKita
|--------------------------------------------------------------------------
*/

// --- FRONT-STORE PUBLIC ROUTES (Pengunjung, Member, Distributor) ---
Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/katalog', [FrontController::class, 'catalog'])->name('catalog');
Route::get('/produk/{slug}', [FrontController::class, 'productDetail'])->name('product.detail');

// AJAX Helper Routes
Route::post('/ajax/check-seller', [FrontController::class, 'checkSeller'])->name('ajax.check-seller');
Route::post('/ajax/check-coupon', [FrontController::class, 'checkCoupon'])->name('ajax.check-coupon');

// Slide-Over Cart Drawer & Cart Page
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah', [CartController::class, 'add'])->name('cart.add');
Route::post('/keranjang/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/keranjang/hapus', [CartController::class, 'remove'])->name('cart.remove');

// Checkout & Konfirmasi (Mendukung Guest Checkout & Member)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/proses', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/pesanan/konfirmasi/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/pesanan/unggah-bukti/{orderNumber}', [CheckoutController::class, 'uploadProof'])->name('checkout.upload-proof');

// --- AUTHENTICATION ROUTES ---
// Customer / Member
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify.post');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Auth
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

// --- MEMBER & DISTRIBUTOR PRIVATE ROUTES (Akun Saya) ---
Route::middleware(['auth'])->prefix('akun')->name('account.')->group(function () {
    Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/pesanan', [AccountController::class, 'orders'])->name('orders');
    Route::get('/pesanan/{orderNumber}', [AccountController::class, 'orderDetail'])->name('order.detail');
    Route::get('/profil', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profil', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
});

// --- BACK-OFFICE / 16 MODUL PANEL ADMIN (Role: Admin) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard Analitik & Statistik DP
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Manajemen Produk Kompleks
    Route::resource('products', AdminProductController::class);

    // 3. Manajemen Brand
    Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
    Route::put('/brands/{id}', [AdminBrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');

    // 4. Manajemen Kategori
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // 5. Kelola Pesanan & Faktur PDF
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{orderId}/verify-payment/{confirmationId}', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::get('/orders/{id}/invoice-pdf', [AdminOrderController::class, 'generateInvoicePdf'])->name('orders.invoice-pdf');
    Route::get('/orders-export-excel', [AdminOrderController::class, 'exportExcel'])->name('orders.export-excel');

    // 6. Mitra Seller & 7. Laporan Penjualan Mitra
    Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
    Route::post('/sellers', [AdminSellerController::class, 'store'])->name('sellers.store');
    Route::put('/sellers/{id}', [AdminSellerController::class, 'update'])->name('sellers.update');
    Route::delete('/sellers/{id}', [AdminSellerController::class, 'destroy'])->name('sellers.destroy');
    Route::get('/sellers/report', [AdminSellerController::class, 'salesReport'])->name('sellers.report');
    Route::get('/sellers/report/export', [AdminSellerController::class, 'exportSalesReport'])->name('sellers.report.export');

    // 8. Kelola Pengguna & Customer Tier
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::put('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

    // 9. Manajemen Kupon Pemasaran
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{id}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // 10 - 16. Manajemen Konten CMS
    Route::prefix('content')->name('content.')->group(function () {
        // Banner Slider
        Route::get('/banners', [AdminContentController::class, 'banners'])->name('banners');
        Route::post('/banners', [AdminContentController::class, 'storeBanner'])->name('banners.store');
        Route::delete('/banners/{id}', [AdminContentController::class, 'destroyBanner'])->name('banners.destroy');

        // Keunggulan Toko
        Route::get('/features', [AdminContentController::class, 'features'])->name('features');
        Route::post('/features', [AdminContentController::class, 'storeFeature'])->name('features.store');
        Route::delete('/features/{id}', [AdminContentController::class, 'destroyFeature'])->name('features.destroy');

        // Media Sosial
        Route::get('/socials', [AdminContentController::class, 'socials'])->name('socials');
        Route::post('/socials', [AdminContentController::class, 'storeSocial'])->name('socials.store');
        Route::delete('/socials/{id}', [AdminContentController::class, 'destroySocial'])->name('socials.destroy');

        // Customer Service (Floating WA)
        Route::get('/customer-services', [AdminContentController::class, 'customerServices'])->name('customer-services');
        Route::post('/customer-services', [AdminContentController::class, 'storeCustomerService'])->name('customer-services.store');
        Route::post('/customer-services/{id}/set-floating', [AdminContentController::class, 'setFloatingWa'])->name('customer-services.set-floating');
        Route::delete('/customer-services/{id}', [AdminContentController::class, 'destroyCustomerService'])->name('customer-services.destroy');

        // Admin Handle
        Route::get('/handles', [AdminContentController::class, 'handles'])->name('handles');
        Route::post('/handles', [AdminContentController::class, 'storeHandle'])->name('handles.store');
        Route::delete('/handles/{id}', [AdminContentController::class, 'destroyHandle'])->name('handles.destroy');

        // Informasi Bank
        Route::get('/banks', [AdminContentController::class, 'banks'])->name('banks');
        Route::post('/banks', [AdminContentController::class, 'storeBank'])->name('banks.store');
        Route::delete('/banks/{id}', [AdminContentController::class, 'destroyBank'])->name('banks.destroy');
    });
});
