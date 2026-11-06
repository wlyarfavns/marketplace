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
use App\Services\CouponService;
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

        // Ambil produk katalog terbaru untuk pelanggan
        $featuredProducts = Product::active()
            ->with(['brand', 'categories', 'images', 'colors', 'variantPrices'])
            ->latest()
            ->take(8)
            ->get();

        return view('front.home', compact(
            'banners', 'brands', 'features', 'categories', 'featuredProducts', 'floatingWa', 'socials'
        ));
    }

    public function catalog(Request $request)
    {
        $query = Product::active()->with(['brand', 'categories', 'images', 'colors']);

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
            $query->where('product_type', $request->type);
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

        return view('front.catalog', compact('products', 'brands', 'categories', 'floatingWa'));
    }

    public function productDetail($slug)
    {
        $product = Product::where('slug', $slug)
            ->with([
                'brand', 'categories', 'images',
                'colors', 'models', 'sizes',
                'variantPrices.productModel', 'variantPrices.productSize',
                'bonuses', 'priceRules',
            ])
            ->firstOrFail();

        $floatingWa = CustomerService::active()->floatingWhatsapp()->first();

        return view('front.product-detail', compact('product', 'floatingWa'));
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
    public function checkCoupon(Request $request, CouponService $couponService)
    {
        $result = $couponService->check(
            (string) $request->input('code', ''),
            (float) $request->input('subtotal', 0),
            (int) $request->input('total_quantity', 1),
            Auth::user()
        );

        if (! $result['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'valid' => true,
            'coupon_id' => $result['coupon_id'],
            'code' => $result['code'],
            'discount_amount' => $result['discount_amount'],
            'discount_formatted' => $result['discount_formatted'],
            'message' => $result['message'],
        ]);
    }
}
