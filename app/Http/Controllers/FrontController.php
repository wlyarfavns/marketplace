<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CustomerService;
use App\Models\Feature;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->ordered()->get();
        $brands = Brand::active()->homepage()->ordered()->get();
        $features = Feature::active()->ordered()->get();
        $categories = Category::active()->withCount('products')->get();
        $floatingWa = CustomerService::active()->floatingWhatsapp()->first();
        $socials = SocialMedia::active()->ordered()->get();

        // Ambil produk yang diizinkan sesuai role user saat ini
        $user = Auth::user();
        $userType = $user ? $user->customer_type : 'umum';

        $featuredProducts = Product::active()
            ->with(['brand', 'categories', 'images', 'colors', 'variantPrices'])
            ->when($userType === 'umum', function ($q) {
                $q->where('product_type', 'ready_stock')->where('visible_to_umum', true);
            })
            ->when($userType === 'member', function ($q) {
                $q->where('visible_to_member', true);
            })
            ->when($userType === 'distributor', function ($q) {
                $q->where('visible_to_distributor', true);
            })
            ->latest()
            ->take(8)
            ->get();

        return view('front.home', compact(
            'banners', 'brands', 'features', 'categories', 'featuredProducts', 'floatingWa', 'socials'
        ));
    }

    public function catalog(Request $request)
    {
        $user = Auth::user();
        $userType = $user ? $user->customer_type : 'umum';

        $query = Product::active()->with(['brand', 'categories', 'images', 'colors']);

        // Batasan Role Produk
        if ($userType === 'umum') {
            $query->where('product_type', 'ready_stock')->where('visible_to_umum', true);
        } elseif ($userType === 'member') {
            $query->where('visible_to_member', true);
        } elseif ($userType === 'distributor') {
            $query->where('visible_to_distributor', true);
        }

        // Filter Pencarian
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter Brand
        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter Jenis Produk (Ready Stock vs PO)
        if ($request->filled('type') && in_array($request->type, ['ready_stock', 'po'])) {
            if ($request->type === 'po' && $userType === 'umum') {
                // Pengunjung dilarang filter PO, diarahkan ke ready_stock
                $query->where('product_type', 'ready_stock');
            } else {
                $query->where('product_type', $request->type);
            }
        }

        // Pengurutan (Sorting)
        if ($request->sort === 'price_asc') {
            $query->orderBy('min_price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $query->orderBy('min_price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        $floatingWa = CustomerService::active()->floatingWhatsapp()->first();

        return view('front.catalog', compact('products', 'brands', 'categories', 'userType', 'floatingWa'));
    }

    public function productDetail($slug)
    {
        $user = Auth::user();
        $userType = $user ? $user->customer_type : 'umum';

        $product = Product::where('slug', $slug)
            ->with([
                'brand', 'categories', 'images',
                'colors', 'models', 'sizes',
                'variantPrices.productModel', 'variantPrices.productSize',
                'bonuses', 'priceRules',
            ])
            ->firstOrFail();

        // Validasi Akses Peran
        if ($userType === 'umum' && ($product->product_type === 'po' || ! $product->visible_to_umum)) {
            return redirect()->route('login')->with('error', 'Produk ini bertipe Pre-Order atau eksklusif. Silakan login sebagai Member atau Hubungi Admin.');
        }

        if ($userType === 'member' && ! $product->visible_to_member) {
            abort(403, 'Produk ini eksklusif untuk tier Distributor.');
        }

        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where('brand_id', $product->brand_id)
            ->when($userType === 'umum', function ($q) {
                $q->where('product_type', 'ready_stock')->where('visible_to_umum', true);
            })
            ->take(4)
            ->get();

        $floatingWa = CustomerService::active()->floatingWhatsapp()->first();

        return view('front.product-detail', compact('product', 'relatedProducts', 'userType', 'floatingWa'));
    }

    // AJAX Cek ID Seller
    public function checkSeller(Request $request)
    {
        $code = trim($request->input('seller_code'));
        if (! $code) {
            return response()->json(['valid' => false, 'message' => 'Kode Seller wajib diisi.'], 400);
        }

        $seller = Seller::where('seller_code', $code)->where('is_active', true)->with('brand')->first();

        if ($seller) {
            return response()->json([
                'valid' => true,
                'id' => $seller->id,
                'name' => $seller->name,
                'brand_name' => $seller->brand ? $seller->brand->name : 'Semua Brand',
                'message' => "Mitra Terverifikasi: {$seller->name} ({$seller->seller_code})",
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Kode Mitra Seller tidak ditemukan atau tidak aktif.',
        ], 404);
    }

    // AJAX Validasi Kupon
    public function checkCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code')));
        $subtotal = (float) $request->input('subtotal', 0);
        $totalQuantity = (int) $request->input('total_quantity', 1);

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher kupon tidak ditemukan atau kadaluarsa.'], 404);
        }

        // Cek Tanggal Berlaku
        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return response()->json(['valid' => false, 'message' => 'Voucher belum mulai berlaku.'], 400);
        }
        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return response()->json(['valid' => false, 'message' => 'Masa berlaku voucher telah habis.'], 400);
        }

        // Cek Kuota Total
        if (! is_null($coupon->quota_total) && $coupon->quota_total <= 0) {
            return response()->json(['valid' => false, 'message' => 'Kuota pemakaian voucher telah habis.'], 400);
        }

        // Cek Target Audiens
        $user = Auth::user();
        if ($coupon->target_audience === 'member_only' && (! $user || $user->customer_type === 'umum')) {
            return response()->json(['valid' => false, 'message' => 'Voucher ini khusus untuk akun Member terdaftar.'], 400);
        }
        if ($coupon->target_audience === 'non_member_only' && $user && $user->customer_type !== 'umum') {
            return response()->json(['valid' => false, 'message' => 'Voucher ini khusus untuk Pengunjung baru / Non-Member.'], 400);
        }

        // Cek Minimal Belanja
        if ($coupon->min_purchase > 0 && $subtotal < $coupon->min_purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal belanja untuk voucher ini adalah Rp '.number_format($coupon->min_purchase, 0, ',', '.'),
            ], 400);
        }

        // Cek Minimal Kuantitas
        if ($coupon->min_quantity > 0 && $totalQuantity < $coupon->min_quantity) {
            return response()->json([
                'valid' => false,
                'message' => "Minimal pembelian {$coupon->min_quantity} item untuk kupon ini.",
            ], 400);
        }

        // Hitung Diskon
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($coupon->discount_value / 100) * $subtotal;
            if ($coupon->max_discount && $discount > $coupon->max_discount) {
                $discount = $coupon->max_discount;
            }
        } else {
            $discount = min($coupon->discount_value, $subtotal);
        }

        return response()->json([
            'valid' => true,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount_amount' => $discount,
            'discount_formatted' => 'Rp '.number_format($discount, 0, ',', '.'),
            'message' => 'Kupon berhasil diterapkan!',
        ]);
    }
}
