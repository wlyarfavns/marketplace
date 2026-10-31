@extends('layouts.app')

@section('content')
<div class="border-b border-gray-100 bg-gray-50 py-4 text-xs text-gray-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-black">Beranda</a>
        <span>/</span>
        <a href="{{ route('catalog') }}" class="hover:text-black">Katalog Sepatu</a>
        <span>/</span>
        <span class="text-gray-900 font-medium truncate">{{ $product->name }}</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12" 
     x-data="productDetailState({
        basePrice: {{ (float) $product->min_price }},
        variantPrices: {!! json_encode($product->variantPrices) !!},
        models: {!! json_encode($product->models) !!},
        sizes: {!! json_encode($product->sizes) !!},
        ruleAdjustment: {{ $product->priceRules->where('is_active', true)->first() ? ($product->priceRules->where('is_active', true)->first()->rule_type === 'cut' ? - (float) $product->priceRules->where('is_active', true)->first()->amount : (float) $product->priceRules->where('is_active', true)->first()->amount) : 0 }},
        bonusMinQty: {{ $product->bonuses->where('is_active', true)->first() ? (int) $product->bonuses->where('is_active', true)->first()->min_quantity : 9999 }}
     })">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
        
        <!-- Kolom Galeri Foto Produk -->
        <div class="space-y-4">
            <div class="aspect-[3/4] bg-neutral-100 border border-gray-200 overflow-hidden relative flex items-center justify-center group">
                @if($product->primary_image_url)
                    <img :src="selectedImage || '{{ $product->primary_image_url }}'" 
                         src="{{ $product->primary_image_url }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                    <i class="fa-solid fa-shoe-prints text-gray-300 text-6xl"></i>
                @endif
                
                <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                    @if($product->product_type === 'ready_stock')
                        <span class="bg-black text-white text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 shadow-xs">
                            Stok Siap Kirim
                        </span>
                    @else
                        <span class="bg-black text-white text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 shadow-xs">
                            Prapesan (PO)
                        </span>
                    @endif

                    @if($product->brand)
                        <span class="bg-white/95 backdrop-blur-xs text-black text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 border border-gray-300 shadow-xs">
                            DP Mulai {{ number_format($product->brand->dp_percentage, 0) }}%
                        </span>
                    @endif
                </div>
            </div>

            <!-- Thumbnail Selector (if multiple images exist) -->
            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->images as $img)
                        <button type="button" 
                                @click="selectedImage = '{{ $img->url }}'"
                                :class="selectedImage === '{{ $img->url }}' ? 'border-black ring-1 ring-black' : 'border-gray-200 hover:border-gray-400'"
                                class="aspect-square bg-neutral-100 border overflow-hidden p-0.5 focus:outline-hidden transition">
                            <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Catatan Produk -->
            @if($product->product_note)
                <div class="p-4 border-l-4 border-black bg-slate-50 text-xs text-gray-700 space-y-1">
                    <p class="font-bold text-black uppercase tracking-wide">Jaminan & Catatan Produk:</p>
                    <p class="leading-relaxed">{{ $product->product_note }}</p>
                </div>
            @endif
        </div>

        <!-- Kolom Informasi & Matriks Pilihan Varian -->
        <div class="space-y-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-black font-bold block mb-1">
                    Brand: {{ $product->brand ? $product->brand->name : 'PasarKita Exclusive' }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-serif font-bold text-gray-950">{{ $product->name }}</h1>
                <p class="text-xs text-gray-500 mt-2">SKU: <span class="font-mono text-gray-900 font-medium">{{ $product->sku }}</span> &bull; Berat: {{ $product->weight_grams }} gram</p>
            </div>

            <!-- Tampilan Harga Dinamis Reaktif Matriks -->
            <div class="p-5 bg-gray-50 border border-gray-200">
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-gray-950" x-text="formatRupiah(currentPrice)"></span>
                    
                    <template x-if="ruleAdjustment !== 0">
                        <span class="text-xs text-black bg-blue-100 px-2 py-0.5 rounded font-bold">
                            Penyesuaian Reseller: <span x-text="ruleAdjustment > 0 ? '+' : ''"></span><span x-text="formatRupiah(ruleAdjustment)"></span>
                        </span>
                    </template>
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Harga otomatis diperbarui berdasarkan perpotongan Edisi Box &times; Ukuran Sepatu yang Anda pilih.</p>
            </div>

            <!-- Form Tambah ke Keranjang -->
            <form @submit.prevent="addToCart()" class="space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <!-- 1. Pemilihan Varian Model / Edisi Box -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <label class="font-black uppercase tracking-wider text-black text-xs">1. Pilih Edisi Kotak / Kemasan *</label>
                        <span class="text-xs font-semibold text-gray-500" x-text="selectedModelName ? 'Terpilih: ' + selectedModelName : 'Wajib dipilih'"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($product->models as $m)
                            <button type="button" 
                                    @click="selectModel({{ $m->id }}, '{{ $m->model_name }}')"
                                    :class="selectedModelId === {{ $m->id }} ? 'border-2 border-black bg-black text-white shadow-md' : 'border-2 border-gray-300 bg-white text-gray-800 hover:border-black hover:shadow-xs'"
                                    class="p-4 text-left transition duration-150 cursor-pointer relative shadow-xs flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-xs uppercase tracking-wide">{{ $m->model_name }}</p>
                                    <span x-show="selectedModelId === {{ $m->id }}" class="text-white text-xs"><i class="fa-solid fa-circle-check"></i></span>
                                </div>
                                @if($m->description)
                                    <p class="text-[11px] mt-1 leading-relaxed" :class="selectedModelId === {{ $m->id }} ? 'text-gray-300' : 'text-gray-500'">{{ $m->description }}</p>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Pemilihan Varian Warna / Colorway -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <label class="font-black uppercase tracking-wider text-black text-xs">2. Pilih Varian Warna *</label>
                        <span class="text-xs font-semibold text-gray-500" x-text="selectedColorName ? 'Terpilih: ' + selectedColorName : 'Wajib dipilih'"></span>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($product->colors as $c)
                            <button type="button" 
                                    @click="selectColor({{ $c->id }}, '{{ $c->color_name }}')"
                                    :class="selectedColorId === {{ $c->id }} ? 'border-2 border-black bg-black text-white shadow-md' : 'border-2 border-gray-300 bg-white text-gray-800 hover:border-black hover:shadow-xs'"
                                    class="flex items-center gap-2.5 px-4 py-2.5 border-2 text-xs font-bold transition duration-150 cursor-pointer shadow-xs active:scale-95">
                                <span class="w-3.5 h-3.5 rounded-full border border-gray-400 shrink-0 shadow-2xs" style="background-color: {{ $c->hex_code }}"></span>
                                <span>{{ $c->color_name }}</span>
                                <i x-show="selectedColorId === {{ $c->id }}" class="fa-solid fa-check text-xs ml-1"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Pemilihan Varian Ukuran (Size Sepatu) -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <label class="font-black uppercase tracking-wider text-black text-xs">3. Pilih Ukuran Sepatu (Standar EU) *</label>
                        <span class="text-xs font-semibold text-gray-500" x-text="selectedSizeLabel ? 'Terpilih: ' + selectedSizeLabel : 'Wajib dipilih'"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach($product->sizes as $s)
                            <button type="button" 
                                    @click="selectSize({{ $s->id }}, '{{ $s->size_label }}')"
                                    :class="selectedSizeId === {{ $s->id }} ? 'border-2 border-black bg-black text-white shadow-md font-black scale-[1.02]' : 'border-2 border-gray-300 bg-white text-gray-900 hover:border-black hover:bg-gray-50 font-bold shadow-xs'"
                                    class="py-3 px-3 text-center border-2 text-xs uppercase transition duration-150 cursor-pointer active:scale-95">
                                <span class="block text-xs">{{ $s->size_label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Jumlah & Aturan Bonus Barang -->
                <div class="pt-4 border-t border-gray-200 space-y-4">
                    <div class="flex items-center gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs uppercase font-black text-gray-900 tracking-wider">Jumlah Pesanan (Pasang)</label>
                            <div class="flex items-center border-2 border-black bg-white shadow-xs">
                                <button type="button" @click="quantity > 1 ? quantity-- : null; recalculatePrice()" class="px-4 py-2.5 text-black hover:bg-gray-100 font-black text-base cursor-pointer active:bg-gray-200 transition">-</button>
                                <input type="number" x-model="quantity" @change="recalculatePrice()" min="1" class="w-14 text-center text-sm font-bold focus:outline-none border-none py-2">
                                <button type="button" @click="quantity++; recalculatePrice()" class="px-4 py-2.5 text-black hover:bg-gray-100 font-black text-base cursor-pointer active:bg-gray-200 transition">+</button>
                            </div>
                        </div>

                        <!-- Informasi Bonus Otomatis -->
                        @if($product->bonuses->where('is_active', true)->count() > 0)
                            @php $bonus = $product->bonuses->where('is_active', true)->first(); @endphp
                            <div class="flex-1 bg-neutral-100 border-2 border-gray-300 p-3.5 text-xs text-black" x-show="quantity >= {{ $bonus->min_quantity }}">
                                <p class="font-bold flex items-center gap-1.5 text-black">
                                    <i class="fa-solid fa-gift"></i> Selamat! Anda Mendapatkan Bonus:
                                </p>
                                <p class="text-[11px] text-gray-700 mt-0.5">{{ $bonus->free_item_name }} ({{ $bonus->free_item_description }})</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi Utama -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <button type="submit" 
                            :disabled="!isReadyToBuy"
                            :class="isReadyToBuy ? 'bg-black text-white hover:bg-neutral-800 shadow-md cursor-pointer border-2 border-black active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed border-2 border-gray-200'"
                            class="flex-1 py-4 px-6 text-xs uppercase tracking-widest font-black transition text-center flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-cart-plus text-sm"></i>
                        <span>Tambah ke Keranjang</span>
                    </button>

                    @if(isset($floatingWa) && $floatingWa)
                        <a :href="'https://wa.me/{{ $floatingWa->phone }}?text=Halo%20Admin,%20saya%20tertarik%20dengan%20sepatu%20' + encodeURIComponent('{{ $product->name }}') + '%20(SKU:%20{{ $product->sku }})'"
                           target="_blank"
                           class="border-2 border-black bg-white text-black hover:bg-black hover:text-white px-6 py-4 text-xs uppercase tracking-widest font-black transition flex items-center justify-center gap-2 shadow-sm cursor-pointer active:scale-[0.98]">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Pesan via WhatsApp</span>
                        </a>
                    @endif
                </div>

                <template x-if="!isReadyToBuy">
                    <p class="text-[11px] text-red-600 font-semibold">* Mohon klik dan pilih Edisi Kotak, Varian Warna, serta Ukuran Sepatu terlebih dahulu.</p>
                </template>
            </form>

            <!-- Deskripsi Tab -->
            <div class="pt-8 border-t border-gray-200 space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-gray-950">Deskripsi & Spesifikasi Sepatu</h3>
                <div class="text-xs text-gray-600 leading-relaxed font-light space-y-2">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    function productDetailState(config) {
        return {
            basePrice: config.basePrice,
            variantPrices: config.variantPrices,
            ruleAdjustment: config.ruleAdjustment,
            bonusMinQty: config.bonusMinQty,

            selectedModelId: null,
            selectedModelName: '',
            selectedColorId: null,
            selectedColorName: '',
            selectedSizeId: null,
            selectedSizeLabel: '',
            selectedImage: '{{ $product->primary_image_url }}',
            quantity: 1,

            currentPrice: config.basePrice + config.ruleAdjustment,

            selectModel(id, name) {
                this.selectedModelId = id;
                this.selectedModelName = name;
                this.recalculatePrice();
            },
            selectColor(id, name) {
                this.selectedColorId = id;
                this.selectedColorName = name;
            },
            selectSize(id, label) {
                this.selectedSizeId = id;
                this.selectedSizeLabel = label;
                this.recalculatePrice();
            },
            recalculatePrice() {
                if (this.selectedModelId && this.selectedSizeId) {
                    const match = this.variantPrices.find(vp => 
                        vp.product_model_id === this.selectedModelId && 
                        vp.product_size_id === this.selectedSizeId
                    );
                    if (match) {
                        this.currentPrice = parseFloat(match.price) + this.ruleAdjustment;
                        return;
                    }
                }
                this.currentPrice = this.basePrice + this.ruleAdjustment;
            },
            get isReadyToBuy() {
                return this.selectedModelId && this.selectedColorId && this.selectedSizeId && this.quantity > 0;
            },
            formatRupiah(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
            },
            addToCart() {
                if (!this.isReadyToBuy) return;

                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        model_id: this.selectedModelId,
                        color_id: this.selectedColorId,
                        size_id: this.selectedSizeId,
                        quantity: this.quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const drawer = Alpine.$data(document.querySelector('body'));
                        drawer.items = data.cart_items;
                        drawer.cartCount = data.cart_count;
                        drawer.subtotal = data.subtotal;
                        drawer.openDrawer();
                    }
                });
            }
        }
    }
</script>
@endsection
