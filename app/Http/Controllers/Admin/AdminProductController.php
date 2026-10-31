<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBonus;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductModel;
use App\Models\ProductPriceRule;
use App\Models\ProductSize;
use App\Models\ProductVariantPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'categories', 'images', 'variantPrices']);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $brands = Brand::active()->get();

        return view('admin.products.index', compact('products', 'brands'));
    }

    public function create()
    {
        $brands = Brand::active()->get();
        $categories = Category::active()->get();

        return view('admin.products.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'sku' => 'required|string|unique:products,sku',
            'product_type' => 'required|in:ready_stock,po',
            'weight_grams' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'product_note' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'categories' => 'required|array',
            // Varian Warna
            'colors' => 'nullable|array',
            'colors.*.name' => 'required_with:colors|string',
            'colors.*.hex' => 'required_with:colors|string',
            // Varian Model
            'models' => 'nullable|array',
            'models.*.name' => 'required_with:models|string',
            'models.*.description' => 'nullable|string',
            // Varian Ukuran
            'sizes' => 'nullable|array',
            'sizes.*.label' => 'required_with:sizes|string',
            // Matriks Harga
            'matrix_prices' => 'nullable|array',
            'base_price' => 'required|numeric|min:0',
            // Bonus & Rule
            'bonus_name' => 'nullable|string',
            'bonus_qty' => 'nullable|integer|min:1',
            'rule_type' => 'nullable|in:add,cut',
            'rule_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']).'-'.Str::random(4),
                'brand_id' => $validated['brand_id'],
                'sku' => strtoupper($validated['sku']),
                'product_type' => $validated['product_type'],
                'weight_grams' => $validated['weight_grams'],
                'description' => $validated['description'] ?? null,
                'product_note' => $validated['product_note'] ?? null,
                'status' => $validated['status'],
                'visible_to_umum' => $request->boolean('visible_to_umum', true),
                'visible_to_member' => $request->boolean('visible_to_member', true),
                'visible_to_distributor' => $request->boolean('visible_to_distributor', true),
                'min_price' => $validated['base_price'],
                'max_price' => $validated['base_price'],
            ]);

            $product->categories()->sync($validated['categories']);

            // Upload Foto Umum
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Simpan Warna
            if (! empty($validated['colors'])) {
                foreach ($validated['colors'] as $idx => $c) {
                    if (! empty($c['name'])) {
                        ProductColor::create([
                            'product_id' => $product->id,
                            'color_name' => $c['name'],
                            'hex_code' => $c['hex'] ?? '#000000',
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Simpan Model & Ukuran & Matriks Harga
            $savedModels = [];
            if (! empty($validated['models'])) {
                foreach ($validated['models'] as $idx => $m) {
                    if (! empty($m['name'])) {
                        $savedModels[] = ProductModel::create([
                            'product_id' => $product->id,
                            'model_name' => $m['name'],
                            'description' => $m['description'] ?? null,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            } else {
                $savedModels[] = ProductModel::create([
                    'product_id' => $product->id,
                    'model_name' => 'Standard',
                    'sort_order' => 1,
                ]);
            }

            $savedSizes = [];
            if (! empty($validated['sizes'])) {
                foreach ($validated['sizes'] as $idx => $s) {
                    if (! empty($s['label'])) {
                        $savedSizes[] = ProductSize::create([
                            'product_id' => $product->id,
                            'size_label' => $s['label'],
                            'is_available' => true,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            } else {
                $savedSizes[] = ProductSize::create([
                    'product_id' => $product->id,
                    'size_label' => 'All Size',
                    'is_available' => true,
                    'sort_order' => 1,
                ]);
            }

            // Buat Matriks Harga
            $allPrices = [];
            foreach ($savedModels as $mod) {
                foreach ($savedSizes as $siz) {
                    $priceKey = "{$mod->model_name}_{$siz->size_label}";
                    $customPrice = $request->input("matrix_prices.{$priceKey}", $validated['base_price']);
                    $customStock = $request->input("matrix_stocks.{$priceKey}", 20);

                    $allPrices[] = (float) $customPrice;

                    ProductVariantPrice::create([
                        'product_id' => $product->id,
                        'product_model_id' => $mod->id,
                        'product_size_id' => $siz->id,
                        'price' => $customPrice,
                        'stock' => $customStock,
                    ]);
                }
            }

            if (! empty($allPrices)) {
                $product->update([
                    'min_price' => min($allPrices),
                    'max_price' => max($allPrices),
                ]);
            }

            // Bonus Barang
            if (! empty($validated['bonus_name'])) {
                ProductBonus::create([
                    'product_id' => $product->id,
                    'free_item_name' => $validated['bonus_name'],
                    'free_item_description' => $request->input('bonus_desc'),
                    'min_quantity' => $validated['bonus_qty'] ?? 1,
                    'is_active' => true,
                ]);
            }

            // Aturan Harga Otomatis
            if (! empty($validated['rule_type']) && ! empty($validated['rule_amount'])) {
                ProductPriceRule::create([
                    'product_id' => $product->id,
                    'rule_type' => $validated['rule_type'],
                    'amount' => $validated['rule_amount'],
                    'description' => $request->input('rule_desc'),
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan lengkap beserta matriks varian!');
    }

    public function edit($id)
    {
        $product = Product::with([
            'brand', 'categories', 'images', 'colors',
            'models', 'sizes', 'variantPrices', 'bonuses', 'priceRules',
        ])->findOrFail($id);

        $brands = Brand::active()->get();
        $categories = Category::active()->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
