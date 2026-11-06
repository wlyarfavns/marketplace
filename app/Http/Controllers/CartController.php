<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductModel;
use App\Models\ProductSize;
use App\Models\ProductVariantPrice;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Ambil Data Cart dari Session
    public function getCart()
    {
        return session()->get('cart', []);
    }

    // Tambah Produk ke Keranjang (Support AJAX & Form)
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'model_id' => 'required|exists:product_models,id',
            'color_id' => 'required|exists:product_colors,id',
            'size_id' => 'required|exists:product_sizes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with(['brand', 'bonuses', 'priceRules', 'images'])->findOrFail($request->product_id);
        $model = ProductModel::findOrFail($request->model_id);
        $color = ProductColor::findOrFail($request->color_id);
        $size = ProductSize::findOrFail($request->size_id);

        // Cari Harga dari Matriks (Model x Size)
        $variantPrice = ProductVariantPrice::where('product_id', $product->id)
            ->where('product_model_id', $model->id)
            ->where('product_size_id', $size->id)
            ->first();

        $basePrice = $variantPrice ? (float) $variantPrice->price : (float) $product->min_price;

        // Hitung Rule Harga Otomatis (jika ada)
        $ruleAdjustment = 0;
        $priceRule = $product->priceRules()->where('is_active', true)->first();
        if ($priceRule) {
            if ($priceRule->rule_type === 'cut') {
                $ruleAdjustment = -(float) $priceRule->amount;
            } elseif ($priceRule->rule_type === 'add') {
                $ruleAdjustment = (float) $priceRule->amount;
            }
        }

        $finalPrice = max(0, $basePrice + $ruleAdjustment);

        // Cek Bonus Barang
        $bonusItem = null;
        $bonus = $product->bonuses()->where('is_active', true)->first();
        if ($bonus && $request->quantity >= $bonus->min_quantity) {
            $bonusItem = $bonus->free_item_name.' ('.$bonus->free_item_description.')';
        }

        // Generate Cart Item Key Unik per kombinasi varian
        $cartKey = "{$product->id}_{$model->id}_{$color->id}_{$size->id}";

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += (int) $request->quantity;
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['unit_price'];

            // Re-evaluasi bonus barang
            if ($bonus && $cart[$cartKey]['quantity'] >= $bonus->min_quantity) {
                $cart[$cartKey]['bonus_item'] = $bonus->free_item_name;
            }
        } else {
            $cart[$cartKey] = [
                'cart_key' => $cartKey,
                'product_id' => $product->id,
                'brand_id' => $product->brand_id,
                'brand_name' => $product->brand ? $product->brand->name : '-',
                'dp_percentage' => $product->brand ? (float) $product->brand->dp_percentage : 30.0,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_type' => $product->product_type,
                'weight_grams' => $product->weight_grams,
                'model_name' => $model->model_name,
                'color_name' => $color->color_name,
                'color_hex' => $color->hex_code,
                'size_name' => $size->size_label,
                'unit_price' => $finalPrice,
                'rule_adjustment' => $ruleAdjustment,
                'quantity' => (int) $request->quantity,
                'subtotal' => $finalPrice * (int) $request->quantity,
                'bonus_item' => $bonusItem,
                'image' => $product->primary_image?->image_path ?? null,
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang!',
                'cart_count' => count($cart),
                'total_quantity' => array_sum(array_column($cart, 'quantity')),
                'subtotal' => array_sum(array_column($cart, 'subtotal')),
                'cart_items' => array_values($cart),
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang belanja.');
    }

    // Update Jumlah Item
    public function update(Request $request)
    {
        $request->validate([
            'cart_key' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_key])) {
            $cart[$request->cart_key]['quantity'] = (int) $request->quantity;
            $cart[$request->cart_key]['subtotal'] = $cart[$request->cart_key]['quantity'] * $cart[$request->cart_key]['unit_price'];

            // Cek Bonus Re-evaluation
            $product = Product::find($cart[$request->cart_key]['product_id']);
            if ($product) {
                $bonus = $product->bonuses()->where('is_active', true)->first();
                if ($bonus && $cart[$request->cart_key]['quantity'] >= $bonus->min_quantity) {
                    $cart[$request->cart_key]['bonus_item'] = $bonus->free_item_name;
                } else {
                    $cart[$request->cart_key]['bonus_item'] = null;
                }
            }

            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => count($cart),
                'total_quantity' => array_sum(array_column($cart, 'quantity')),
                'subtotal' => array_sum(array_column($cart, 'subtotal')),
                'item_subtotal' => isset($cart[$request->cart_key]) ? $cart[$request->cart_key]['subtotal'] : 0,
            ]);
        }

        return redirect()->back();
    }

    // Hapus Item dari Keranjang
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => count($cart),
                'total_quantity' => array_sum(array_column($cart, 'quantity')),
                'subtotal' => array_sum(array_column($cart, 'subtotal')),
            ]);
        }

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    // Halaman Keranjang Lengkap
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $totalWeight = array_sum(array_map(fn ($item) => $item['weight_grams'] * $item['quantity'], $cart));

        return view('front.cart', compact('cart', 'subtotal', 'totalWeight'));
    }
}
