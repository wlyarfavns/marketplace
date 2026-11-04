@extends('layouts.admin')

@section('page_title', 'Edit Produk — ' . $product->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="productEditState()">
    
    <div class="flex justify-between items-center">
        <div>
            <span class="text-xs font-mono font-bold text-slate-500 uppercase">{{ $product->sku }}</span>
            <h2 class="text-lg font-bold text-slate-900 mt-0.5">Edit Produk & Varian: {{ $product->name }}</h2>
            <p class="text-xs text-slate-500">Perbarui informasi katalog, galeri foto, varian warna, edisi box, dan matriks harga.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('product.detail', $product->slug) }}" target="_blank" class="text-xs font-semibold text-slate-700 hover:text-black flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 bg-white">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat di Toko
            </a>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-black">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- 1. Informasi Utama & Visibilitas Role -->
        <div class="bg-white border border-slate-200 p-6 space-y-6">
            <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">1. Data Induk Produk & Visibilitas Role</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="sm:col-span-2 space-y-1">
                    <label class="font-semibold text-slate-700">Nama Produk *</label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}"
                           class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800 font-semibold text-slate-900">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Kode SKU Unik *</label>
                    <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}"
                           class="w-full px-3 py-2 border border-slate-300 uppercase font-mono focus:outline-none focus:border-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Brand Mitra *</label>
                    <select name="brand_id" required class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>
                                {{ $b->name }} (DP {{ number_format($b->dp_percentage, 0) }}%)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Jenis Stok *</label>
                    <select name="product_type" required class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                        <option value="ready_stock" {{ $product->product_type === 'ready_stock' ? 'selected' : '' }}>Ready Stock</option>
                        <option value="po" {{ $product->product_type === 'po' ? 'selected' : '' }}>Pre-Order (PO)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Berat (Gram) *</label>
                    <input type="number" name="weight_grams" required value="{{ old('weight_grams', $product->weight_grams) }}" min="0"
                           class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Status Produk *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                        <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active (Tampil)</option>
                        <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Disembunyikan)</option>
                        <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Multi Kategori -->
            <div class="space-y-1 text-xs">
                <label class="font-semibold text-slate-700">Pilih Kategori (Bisa lebih dari satu) *</label>
                <div class="flex flex-wrap gap-3 pt-1">
                    @php $productCategoryIds = $product->categories->pluck('id')->toArray(); @endphp
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 bg-slate-50 cursor-pointer text-xs hover:border-slate-400">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                   {{ in_array($cat->id, $productCategoryIds) ? 'checked' : '' }}
                                   class="text-black focus:ring-0">
                            <span>{{ $cat->name }} ({{ ucfirst($cat->type) }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Visibilitas Katalog Pelanggan -->
            <div class="p-4 bg-slate-50 border border-slate-200 space-y-2 text-xs">
                <label class="font-bold uppercase tracking-wider text-slate-800 block">Katalog Pelanggan</label>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="visible_to_umum" value="1" {{ $product->visible_to_umum ? 'checked' : '' }} class="text-black focus:ring-0">
                        <input type="hidden" name="visible_to_member" value="1">
                        <input type="hidden" name="visible_to_distributor" value="1">
                        <span class="font-semibold text-slate-800">Tampilkan produk ini di katalog toko untuk pelanggan</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Catatan Khusus Produk (Muncul di layar pembeli)</label>
                    <input type="text" name="product_note" value="{{ old('product_note', $product->product_note) }}"
                           placeholder="Contoh: 100% Brand New In Box (BNIB)..." 
                           class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Harga Dasar Acuan (Rp) *</label>
                    <input type="number" name="base_price" required x-model="basePrice" min="0"
                           class="w-full px-3 py-2 border border-slate-300 font-bold focus:outline-none focus:border-slate-800">
                </div>
            </div>

            <div class="space-y-1 text-xs">
                <label class="font-semibold text-slate-700">Deskripsi Lengkap Produk</label>
                <textarea name="description" rows="3" placeholder="Jelaskan karakteristik material, keaslian, detail siluet..." 
                          class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <!-- 2. Galeri Foto Produk -->
        <div class="bg-white border border-slate-200 p-6 space-y-4 text-xs">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">2. Galeri Foto Produk</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Pilih foto utama, hapus foto yang tidak diperlukan, atau unggah foto baru.</p>
            </div>

            @if($product->images->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                    @foreach($product->images as $img)
                        <div class="border border-slate-200 rounded p-2 bg-slate-50 space-y-2 relative group">
                            <div class="aspect-square bg-white flex items-center justify-center overflow-hidden border border-slate-100">
                                <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                            </div>
                            <div class="space-y-1 text-[11px]">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }} class="text-black">
                                    <span class="font-semibold {{ $img->is_primary ? 'text-emerald-700' : 'text-slate-600' }}">
                                        {{ $img->is_primary ? 'Utama' : 'Jadikan Utama' }}
                                    </span>
                                </label>
                                <label class="flex items-center gap-1.5 text-red-600 cursor-pointer pt-1 border-t border-slate-200">
                                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="text-red-600">
                                    <span>Hapus Foto</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 italic">Belum ada foto yang diunggah untuk produk ini.</p>
            @endif

            <div class="pt-3 border-t border-slate-100 space-y-1">
                <label class="font-semibold text-slate-700">Unggah Tambahan Foto Produk (Bisa pilih beberapa sekaligus)</label>
                <input type="file" name="images[]" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-slate-300 bg-white file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:bg-slate-900 file:text-white">
            </div>
        </div>

        <!-- 3. Repeater Varian Warna & Dynamic Colorway Image -->
        <div class="bg-white border border-slate-200 p-6 space-y-4 text-xs">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <div>
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">3. Varian Warna & Foto Colorway Dinamis</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Tentukan nama warna, warna hex, dan jalur/file gambar sepatu yang otomatis berganti saat warna dipilih.</p>
                </div>
                <button type="button" @click="addColor()" class="text-xs bg-slate-100 hover:bg-black hover:text-white px-3 py-1 font-semibold uppercase">
                    + Tambah Warna
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(col, index) in colors" :key="index">
                    <div class="p-3 bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'colors['+index+'][name]'" x-model="col.name" placeholder="Nama Warna (Contoh: Triple White)" required class="flex-1 px-3 py-2 border border-slate-300 font-semibold">
                            <input type="color" :name="'colors['+index+'][hex]'" x-model="col.hex" class="w-10 h-9 p-0.5 border border-slate-300 cursor-pointer">
                            <input type="text" :name="'colors['+index+'][hex_text]'" x-model="col.hex" placeholder="#FFFFFF" class="w-24 px-3 py-2 border border-slate-300 font-mono">
                            <button type="button" @click="removeColor(index)" class="text-red-500 hover:text-red-700 px-2 py-1" x-show="colors.length > 1">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-3 pt-1 border-t border-slate-200 text-[11px]">
                            <span class="font-semibold text-slate-600 shrink-0">Foto Khusus Warna Ini:</span>
                            <input type="text" :name="'colors['+index+'][color_image_path]'" x-model="col.color_image_path" placeholder="images/shoes/nike-dunk-white.jpg" class="flex-1 px-2.5 py-1.5 border border-slate-300 font-mono text-[10px]">
                            <input type="file" :name="'colors['+index+'][image_file]'" accept="image/*" class="w-56 text-[10px]">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 4. Repeater Model & Repeater Ukuran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            
            <!-- Repeater Model -->
            <div class="bg-white border border-slate-200 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">4. Varian Model / Edisi Box</h3>
                    <button type="button" @click="addModel()" class="text-xs bg-slate-100 hover:bg-black hover:text-white px-2.5 py-1 font-semibold uppercase">+ Model</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(mod, index) in models" :key="index">
                        <div class="p-3 bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex justify-between items-center">
                                <input type="text" :name="'models['+index+'][name]'" x-model="mod.name" placeholder="Nama Model (Contoh: Standard OG Box)" required class="w-full px-2.5 py-1.5 border border-slate-300 text-xs font-semibold">
                                <button type="button" @click="removeModel(index)" class="text-red-500 ml-2" x-show="models.length > 1">&times;</button>
                            </div>
                            <input type="text" :name="'models['+index+'][description]'" x-model="mod.desc" placeholder="Keterangan singkat..." class="w-full px-2.5 py-1.5 border border-slate-300 text-xs">
                        </div>
                    </template>
                </div>
            </div>

            <!-- Repeater Ukuran -->
            <div class="bg-white border border-slate-200 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">5. Varian Ukuran (Size EU)</h3>
                    <button type="button" @click="addSize()" class="text-xs bg-slate-100 hover:bg-black hover:text-white px-2.5 py-1 font-semibold uppercase">+ Ukuran</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(siz, index) in sizes" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" :name="'sizes['+index+'][label]'" x-model="siz.label" placeholder="Label Size (EU 40, EU 41)" required class="flex-1 px-3 py-1.5 border border-slate-300 text-xs uppercase font-semibold">
                            <button type="button" @click="removeSize(index)" class="text-red-500 px-2" x-show="sizes.length > 1">&times;</button>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- 5. Matriks Harga & Stok (Model x Ukuran) -->
        <div class="bg-white border border-slate-200 p-6 space-y-4 text-xs">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">6. Matriks Harga & Stok (Model &times; Ukuran)</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Tentukan harga dan stok spesifik perpotongan varian model dan ukuran.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-3">Model Box</th>
                            <th class="p-3">Ukuran (Size)</th>
                            <th class="p-3">Harga Varian (Rp)</th>
                            <th class="p-3">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="mod in models" :key="mod.name">
                            <template x-for="siz in sizes" :key="mod.name + '_' + siz.label">
                                <tr>
                                    <td class="p-3 font-semibold text-slate-800" x-text="mod.name || 'Model'"></td>
                                    <td class="p-3 font-semibold uppercase text-slate-800" x-text="siz.label || 'Size'"></td>
                                    <td class="p-3">
                                        <input type="number" 
                                               :name="'matrix_prices['+mod.name+'_'+siz.label+']'" 
                                               :value="getMatrixPrice(mod.name, siz.label)" 
                                               min="0" 
                                               class="w-36 px-2.5 py-1.5 border border-slate-300 font-bold">
                                    </td>
                                    <td class="p-3">
                                        <input type="number" 
                                               :name="'matrix_stocks['+mod.name+'_'+siz.label+']'" 
                                               :value="getMatrixStock(mod.name, siz.label)" 
                                               min="0" 
                                               class="w-20 px-2.5 py-1.5 border border-slate-300">
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. Bonus Barang & Aturan Harga Otomatis -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            
            <!-- Bonus FREE Item -->
            @php $currentBonus = $product->bonuses->first(); @endphp
            <div class="bg-white border border-slate-200 p-6 space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    7. Hadiah Gratis (FREE Item / Bonus)
                </h3>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Barang Gratis</label>
                    <input type="text" name="bonus_name" value="{{ old('bonus_name', $currentBonus ? $currentBonus->free_item_name : '') }}" placeholder="Contoh: Premium Sneaker Cleaner & Crep Wipe" class="w-full px-3 py-2 border border-slate-300">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Minimal Pembelian (Pasang)</label>
                    <input type="number" name="bonus_qty" value="{{ old('bonus_qty', $currentBonus ? $currentBonus->min_quantity : 2) }}" min="1" class="w-full px-3 py-2 border border-slate-300">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Keterangan Bonus</label>
                    <input type="text" name="bonus_desc" value="{{ old('bonus_desc', $currentBonus ? $currentBonus->free_item_description : '') }}" placeholder="Gratis 1 set pembersih sneaker..." class="w-full px-3 py-2 border border-slate-300">
                </div>
            </div>

            <!-- Aturan Harga Otomatis -->
            @php $currentRule = $product->priceRules->first(); @endphp
            <div class="bg-white border border-slate-200 p-6 space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    8. Aturan Harga Otomatis (Diskon / Tambahan)
                </h3>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Tipe Penyesuaian</label>
                    <select name="rule_type" class="w-full px-3 py-2 border border-slate-300 bg-white">
                        <option value="">Tidak ada penyesuaian</option>
                        <option value="cut" {{ ($currentRule && $currentRule->rule_type === 'cut') ? 'selected' : '' }}>Potong Harga (Diskon Otomatis)</option>
                        <option value="add" {{ ($currentRule && $currentRule->rule_type === 'add') ? 'selected' : '' }}>Tambah Harga (Biaya Tambahan)</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Besaran Nominal (Rp)</label>
                    <input type="number" name="rule_amount" value="{{ old('rule_amount', $currentRule ? (int)$currentRule->amount : 0) }}" min="0" class="w-full px-3 py-2 border border-slate-300">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Keterangan Aturan</label>
                    <input type="text" name="rule_desc" value="{{ old('rule_desc', $currentRule ? $currentRule->description : '') }}" placeholder="Potongan otomatis grosir..." class="w-full px-3 py-2 border border-slate-300">
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-3 border border-slate-300 text-xs uppercase font-bold text-slate-600 hover:text-black">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 bg-slate-900 text-white text-xs uppercase tracking-widest font-bold hover:bg-slate-800 transition shadow-sm">
                Perbarui & Simpan Perubahan
            </button>
        </div>

    </form>
</div>

@php
    $initialColors = $product->colors->map(function ($c) {
        return [
            'name' => $c->color_name,
            'hex' => $c->hex_code ?? '#000000',
            'color_image_path' => $c->color_image_path ?? '',
        ];
    })->values();

    $initialModels = $product->models->map(function ($m) {
        return [
            'name' => $m->model_name,
            'desc' => $m->description ?? '',
        ];
    })->values();

    $initialSizes = $product->sizes->map(function ($s) {
        return [
            'label' => $s->size_label,
        ];
    })->values();

    $matrixPriceMap = [];
    $matrixStockMap = [];
    foreach ($product->variantPrices as $vp) {
        $modName = $vp->productModel ? $vp->productModel->model_name : '';
        $sizLabel = $vp->productSize ? $vp->productSize->size_label : '';
        if ($modName && $sizLabel) {
            $k = "{$modName}_{$sizLabel}";
            $matrixPriceMap[$k] = (float) $vp->price;
            $matrixStockMap[$k] = (int) $vp->stock;
        }
    }
@endphp

<script>
    function productEditState() {
        const existingPrices = @json($matrixPriceMap);
        const existingStocks = @json($matrixStockMap);

        return {
            basePrice: {{ (float) ($product->min_price ?? 1000000) }},
            colors: @json($initialColors->isEmpty() ? [['name' => 'Original', 'hex' => '#000000', 'color_image_path' => '']] : $initialColors),
            models: @json($initialModels->isEmpty() ? [['name' => 'Standard Box', 'desc' => 'Box resmi standar BNIB']] : $initialModels),
            sizes: @json($initialSizes->isEmpty() ? [['label' => 'EU 40'], ['label' => 'EU 41'], ['label' => 'EU 42']] : $initialSizes),

            getMatrixPrice(modelName, sizeLabel) {
                const k = modelName + '_' + sizeLabel;
                if (existingPrices[k] !== undefined) {
                    return existingPrices[k];
                }
                return this.basePrice;
            },
            getMatrixStock(modelName, sizeLabel) {
                const k = modelName + '_' + sizeLabel;
                if (existingStocks[k] !== undefined) {
                    return existingStocks[k];
                }
                return 20;
            },
            addColor() {
                this.colors.push({ name: '', hex: '#000000', color_image_path: '' });
            },
            removeColor(idx) {
                if (this.colors.length > 1) this.colors.splice(idx, 1);
            },
            addModel() {
                this.models.push({ name: '', desc: '' });
            },
            removeModel(idx) {
                if (this.models.length > 1) this.models.splice(idx, 1);
            },
            addSize() {
                this.sizes.push({ label: '' });
            },
            removeSize(idx) {
                if (this.sizes.length > 1) this.sizes.splice(idx, 1);
            }
        }
    }
</script>
@endsection

