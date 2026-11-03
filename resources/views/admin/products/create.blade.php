@extends('layouts.admin')

@section('page_title', 'Tambah Produk Baru — Form Komprehensif')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="productCreateState()">
    
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Form Produk & Matriks Varian</h2>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi produk, warna, repeater model, ukuran, dan matriks harga.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-black">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. Informasi Utama & Visibilitas Tier -->
        <div class="bg-white border border-slate-200 p-6 space-y-6">
            <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">1. Data Induk Produk & Visibilitas Role</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="sm:col-span-2 space-y-1">
                    <label class="font-semibold text-slate-700">Nama Produk *</label>
                    <input type="text" name="name" required placeholder="Contoh: Madina Linen Flow Abaya" 
                           class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Kode SKU Unik *</label>
                    <input type="text" name="sku" required placeholder="ALM-ABY-001" 
                           class="w-full px-3 py-2 border border-slate-300 uppercase font-mono focus:outline-none focus:border-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Brand Mitra *</label>
                    <select name="brand_id" required class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} (DP {{ number_format($b->dp_percentage, 0) }}%)</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Jenis Stok *</label>
                    <select name="product_type" required class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                        <option value="ready_stock">Ready Stock</option>
                        <option value="po">Pre-Order (PO)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Berat (Gram) *</label>
                    <input type="number" name="weight_grams" required value="500" min="0"
                           class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Status Produk *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                        <option value="active">Active (Tampil)</option>
                        <option value="draft">Draft (Disembunyikan)</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Multi Kategori -->
            <div class="space-y-1 text-xs">
                <label class="font-semibold text-slate-700">Pilih Kategori (Bisa lebih dari satu) *</label>
                <div class="flex flex-wrap gap-3 pt-1">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 bg-slate-50 cursor-pointer text-xs">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" class="text-black focus:ring-0">
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
                        <input type="checkbox" name="visible_to_umum" value="1" checked class="text-black focus:ring-0">
                        <input type="hidden" name="visible_to_member" value="1">
                        <input type="hidden" name="visible_to_distributor" value="1">
                        <span class="font-semibold text-slate-800">Tampilkan produk ini di katalog toko untuk pelanggan</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Catatan Khusus Produk (Muncul di layar pembeli)</label>
                    <input type="text" name="product_note" placeholder="Contoh: Bahan pure linen, free matching belt..." 
                           class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Harga Dasar Acuan (Rp) *</label>
                    <input type="number" name="base_price" required x-model="basePrice" min="0" value="350000"
                           class="w-full px-3 py-2 border border-slate-300 font-bold focus:outline-none focus:border-slate-800">
                </div>
            </div>

            <div class="space-y-1 text-xs">
                <label class="font-semibold text-slate-700">Deskripsi Lengkap Produk</label>
                <textarea name="description" rows="3" placeholder="Jelaskan detail potongan, karakteristik kain, instruksi perawatan..." 
                          class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800"></textarea>
            </div>

            <div class="space-y-1 text-xs">
                <label class="font-semibold text-slate-700">Unggah Foto Produk Umum (Bisa pilih beberapa) *</label>
                <input type="file" name="images[]" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-slate-300 bg-white file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:bg-slate-900 file:text-white">
            </div>
        </div>

        <!-- 2. Repeater Varian Warna -->
        <div class="bg-white border border-slate-200 p-6 space-y-4 text-xs">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">2. Varian Warna (Nama & Kode Hex)</h3>
                <button type="button" @click="addColor()" class="text-xs bg-slate-100 hover:bg-black hover:text-white px-3 py-1 font-semibold uppercase">
                    + Tambah Warna
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(col, index) in colors" :key="index">
                    <div class="flex items-center gap-3">
                        <input type="text" :name="'colors['+index+'][name]'" x-model="col.name" placeholder="Nama Warna (Contoh: Off White)" required class="flex-1 px-3 py-2 border border-slate-300">
                        <input type="color" :name="'colors['+index+'][hex]'" x-model="col.hex" class="w-10 h-9 p-0.5 border border-slate-300 cursor-pointer">
                        <input type="text" :name="'colors['+index+'][hex_text]'" x-model="col.hex" placeholder="#FFFFFF" class="w-24 px-3 py-2 border border-slate-300 font-mono">
                        <button type="button" @click="removeColor(index)" class="text-red-500 hover:text-red-700 px-2 py-1">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- 3. Repeater Model & Repeater Ukuran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            
            <!-- Repeater Model -->
            <div class="bg-white border border-slate-200 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">3. Varian Model / Cutting</h3>
                    <button type="button" @click="addModel()" class="text-xs bg-slate-100 hover:bg-black hover:text-white px-2.5 py-1 font-semibold uppercase">+ Model</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(mod, index) in models" :key="index">
                        <div class="p-3 bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex justify-between items-center">
                                <input type="text" :name="'models['+index+'][name]'" x-model="mod.name" placeholder="Nama Model (Contoh: Standard Cut)" required class="w-full px-2.5 py-1.5 border border-slate-300 text-xs font-semibold">
                                <button type="button" @click="removeModel(index)" class="text-red-500 ml-2" x-show="models.length > 1">&times;</button>
                            </div>
                            <input type="text" :name="'models['+index+'][description]'" x-model="mod.desc" placeholder="Keterangan singkat model..." class="w-full px-2.5 py-1.5 border border-slate-300 text-xs">
                        </div>
                    </template>
                </div>
            </div>

            <!-- Repeater Ukuran -->
            <div class="bg-white border border-slate-200 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">4. Varian Ukuran (Size)</h3>
                    <button type="button" @click="addSize()" class="text-xs bg-slate-100 hover:bg-black hover:text-white px-2.5 py-1 font-semibold uppercase">+ Ukuran</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(siz, index) in sizes" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" :name="'sizes['+index+'][label]'" x-model="siz.label" placeholder="Label Size (Contoh: S, M, All Size)" required class="flex-1 px-3 py-1.5 border border-slate-300 text-xs uppercase font-semibold">
                            <button type="button" @click="removeSize(index)" class="text-red-500 px-2" x-show="sizes.length > 1">&times;</button>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- 4. Matriks Harga Inti (Model x Ukuran) Sesuai PRD Poin 4.2.2 & 6 -->
        <div class="bg-white border border-slate-200 p-6 space-y-4 text-xs">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">5. Matriks Harga & Stok (Model &times; Ukuran)</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Tentukan harga dan stok spesifik perpotongan varian model dan ukuran.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-3">Model Potongan</th>
                            <th class="p-3">Ukuran (Size)</th>
                            <th class="p-3">Harga Varian (Rp)</th>
                            <th class="p-3">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="mod in models" :key="mod.name">
                            <template x-for="siz in sizes" :key="mod.name + '_' + siz.label">
                                <tr>
                                    <td class="p-3 font-semibold text-slate-800" x-text="mod.name || 'Model Baru'"></td>
                                    <td class="p-3 font-semibold uppercase text-slate-800" x-text="siz.label || 'Size Baru'"></td>
                                    <td class="p-3">
                                        <input type="number" :name="'matrix_prices['+mod.name+'_'+siz.label+']'" :value="basePrice" min="0" class="w-36 px-2.5 py-1.5 border border-slate-300 font-bold">
                                    </td>
                                    <td class="p-3">
                                        <input type="number" :name="'matrix_stocks['+mod.name+'_'+siz.label+']'" value="25" min="0" class="w-20 px-2.5 py-1.5 border border-slate-300">
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Bonus Barang & Aturan Harga Otomatis Sesuai PRD -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            
            <!-- Bonus FREE Item -->
            <div class="bg-white border border-slate-200 p-6 space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    6. Hadiah Gratis (FREE Item / Bonus)
                </h3>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Barang Gratis</label>
                    <input type="text" name="bonus_name" placeholder="Contoh: Premium Sneaker Cleaner & Crep Wipe" class="w-full px-3 py-2 border border-slate-300">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Minimal Pembelian (Pasang)</label>
                    <input type="number" name="bonus_qty" value="2" min="1" class="w-full px-3 py-2 border border-slate-300">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Keterangan Bonus</label>
                    <input type="text" name="bonus_desc" placeholder="Gratis 1 set pembersih sneaker setiap beli min 2 pasang" class="w-full px-3 py-2 border border-slate-300">
                </div>
            </div>

            <!-- Aturan Harga Otomatis (Rule Harga) -->
            <div class="bg-white border border-slate-200 p-6 space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    7. Aturan Harga Otomatis (Diskon / Tambahan)
                </h3>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Tipe Penyesuaian</label>
                    <select name="rule_type" class="w-full px-3 py-2 border border-slate-300 bg-white">
                        <option value="">Tidak ada penyesuaian</option>
                        <option value="cut">Potong Harga (Diskon Otomatis)</option>
                        <option value="add">Tambah Harga (Biaya Tambahan)</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Besaran Nominal (Rp)</label>
                    <input type="number" name="rule_amount" value="0" min="0" class="w-full px-3 py-2 border border-slate-300">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Keterangan Aturan</label>
                    <input type="text" name="rule_desc" placeholder="Potongan otomatis khusus grosir mitra" class="w-full px-3 py-2 border border-slate-300">
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-3 border border-slate-300 text-xs uppercase font-bold text-slate-600 hover:text-black">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 bg-slate-900 text-white text-xs uppercase tracking-widest font-bold hover:bg-slate-800 transition">
                Simpan & Terbitkan Produk
            </button>
        </div>

    </form>
</div>

<script>
    function productCreateState() {
        return {
            basePrice: 350000,
            colors: [
                { name: 'Pure White', hex: '#FFFFFF' },
                { name: 'Onyx Black', hex: '#1A1A1A' }
            ],
            models: [
                { name: 'Standard Cut', desc: 'Potongan klasik butik' },
                { name: 'Pocket Edition', desc: 'Dengan saku samping' }
            ],
            sizes: [
                { label: 'S-M' },
                { label: 'L-XL' }
            ],
            addColor() {
                this.colors.push({ name: '', hex: '#000000' });
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

