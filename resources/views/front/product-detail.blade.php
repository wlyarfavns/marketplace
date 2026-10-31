@extends('layouts.app')

@section('content')
<script>
    window.productPageConfig = {
        basePrice: {{ (float) $product->min_price }},
        variantPrices: @json($product->variantPrices),
        models: @json($product->models),
        colors: @json($product->colors),
        sizes: @json($product->sizes),
        defaultImage: '{{ $product->primary_image_url }}',
        ruleAdjustment: {{ $product->priceRules->where('is_active', true)->first() ? ($product->priceRules->where('is_active', true)->first()->rule_type === 'cut' ? - (float) $product->priceRules->where('is_active', true)->first()->amount : (float) $product->priceRules->where('is_active', true)->first()->amount) : 0 }},
        bonusMinQty: {{ $product->bonuses->where('is_active', true)->first() ? (int) $product->bonuses->where('is_active', true)->first()->min_quantity : 9999 }}
    };

    function productDetailState(config) {
        const initialModel = (config.models && config.models.length > 0) ? config.models[0] : null;
        const initialColor = (config.colors && config.colors.length > 0) ? config.colors[0] : null;
        const initialSize = (config.sizes && config.sizes.length > 0) ? config.sizes[0] : null;

        return {
            basePrice: config.basePrice || 0,
            variantPrices: config.variantPrices || [],
            ruleAdjustment: config.ruleAdjustment || 0,
            bonusMinQty: config.bonusMinQty || 9999,

            selectedModelId: initialModel ? initialModel.id : null,
            selectedModelName: initialModel ? initialModel.model_name : '',
            selectedColorId: initialColor ? initialColor.id : null,
            selectedColorName: initialColor ? initialColor.color_name : '',
            selectedSizeId: initialSize ? initialSize.id : null,
            selectedSizeLabel: initialSize ? initialSize.size_label : '',
            selectedImage: config.defaultImage || '',
            quantity: 1,

            currentPrice: (config.basePrice || 0) + (config.ruleAdjustment || 0),

            init() {
                this.recalculatePrice();
            },

            selectModel(id, name) {
                this.selectedModelId = id;
                this.selectedModelName = name;
                this.recalculatePrice();
            },
            selectColor(id, name, image = null) {
                this.selectedColorId = id;
                this.selectedColorName = name;
                if (image && image.trim() !== '') {
                    this.selectedImage = image;
                }
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
                        const rawPrice = match.price_umum || match.price_member || match.price_distributor || match.price || this.basePrice;
                        this.currentPrice = parseFloat(rawPrice) + this.ruleAdjustment;
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
        };
    }
    window.productDetailState = productDetailState;
</script>

<div class="border-b border-gray-100 bg-gray-50 py-4 text-xs text-gray-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-black">Beranda</a>
        <span>/</span>
        <a href="{{ route('catalog') }}" class="hover:text-black">Katalog Sepatu</a>
        <span>/</span>
        <span class="text-gray-900 font-medium truncate">{{ $product->name }}</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12" x-data="productDetailState(window.productPageConfig)">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
        
        <!-- Kolom Galeri Foto Produk -->
        <div class="space-y-4">
            <div class="aspect-[4/3] sm:aspect-square bg-[#FAFAFA] rounded-3xl border border-neutral-200/90 p-6 sm:p-10 overflow-hidden relative flex items-center justify-center group shadow-xs">
                @if($product->primary_image_url)
                    <img :src="selectedImage || '{{ $product->primary_image_url }}'" 
                         src="{{ $product->primary_image_url }}" 
                         alt="{{ $product->name }}" 
                         onerror="if (this.src !== '{{ $product->primary_image_url }}') this.src = '{{ $product->primary_image_url }}';"
                         class="w-full h-full object-contain group-hover:scale-105 transition-all duration-500 filter drop-shadow-xs">
                @else
                    <i class="fa-solid fa-shoe-prints text-neutral-300 text-6xl"></i>
                @endif
                
                <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                    @if($product->product_type === 'ready_stock')
                        <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase font-semibold tracking-wider px-3 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0000FF]"></span>
                            Ready Stock
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase font-semibold tracking-wider px-3 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                            <i class="fa-regular fa-clock text-[9px] text-neutral-400"></i>
                            Pre-Order (PO)
                        </span>
                    @endif

                    @if($product->brand)
                        <span class="bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase tracking-wider font-semibold px-3 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                            DP Min {{ number_format($product->brand->dp_percentage, 0) }}%
                        </span>
                    @endif
                </div>

                <span class="absolute top-4 right-4 inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md border border-neutral-200/80 shadow-2xs z-10">
                    <i class="fa-solid fa-check text-[9px] text-[#0000FF]"></i> 100% Original
                </span>
            </div>

            <!-- Thumbnail Selector (if multiple images exist) -->
            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->images as $img)
                        <button type="button" 
                                @click="selectedImage = '{{ $img->url }}'"
                                :class="selectedImage === '{{ $img->url }}' ? 'border-[#0000FF] ring-2 ring-[#0000FF]/30' : 'border-neutral-200 hover:border-neutral-400'"
                                class="aspect-square bg-[#FAFAFA] rounded-xl border overflow-hidden p-2 focus:outline-hidden transition cursor-pointer">
                            <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Catatan Produk -->
            @if($product->product_note)
                <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50/70 text-xs text-neutral-700 space-y-1">
                    <p class="font-bold text-neutral-950 uppercase tracking-wide">Jaminan & Catatan Produk:</p>
                    <p class="leading-relaxed font-normal">{{ $product->product_note }}</p>
                </div>
            @endif
        </div>

        <!-- Kolom Informasi & Matriks Pilihan Varian -->
        <div class="space-y-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold block mb-1">
                    Brand: {{ $product->brand ? $product->brand->name : 'PasarKita Exclusive' }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-950 tracking-tight leading-tight">{{ $product->name }}</h1>
                <p class="text-xs text-neutral-500 mt-2">SKU: <span class="font-mono text-neutral-900 font-medium">{{ $product->sku }}</span> &bull; Berat: {{ $product->weight_grams }} gram</p>
            </div>

            <!-- Tampilan Harga Dinamis Reaktif Matriks -->
            <div class="p-6 bg-neutral-50/70 rounded-2xl border border-neutral-200/80">
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl sm:text-4xl font-extrabold text-neutral-950 tracking-tight" x-text="formatRupiah(currentPrice)"></span>
                    
                    <template x-if="ruleAdjustment !== 0">
                        <span class="text-xs text-neutral-900 bg-neutral-200/80 px-2.5 py-0.5 rounded-md font-bold">
                            Penyesuaian Reseller: <span x-text="ruleAdjustment > 0 ? '+' : ''"></span><span x-text="formatRupiah(ruleAdjustment)"></span>
                        </span>
                    </template>
                </div>
                <p class="text-[11px] text-neutral-500 mt-1">Harga otomatis diperbarui berdasarkan perpotongan Edisi Box &times; Ukuran Sepatu yang Anda pilih.</p>
            </div>

            <!-- Form Tambah ke Keranjang -->
            <form @submit.prevent="addToCart()" class="space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <!-- 1. Pemilihan Varian Model / Edisi Box -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <label class="font-bold uppercase tracking-wider text-neutral-900">1. Pilih Edisi Box / Kemasan *</label>
                        <span class="text-neutral-500 font-medium" x-text="selectedModelName ? 'Terpilih: ' + selectedModelName : 'Wajib dipilih'"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($product->models as $m)
                            <button type="button" 
                                    @click="selectModel({{ $m->id }}, {!! json_encode($m->model_name) !!})"
                                    :class="selectedModelId === {{ $m->id }} ? 'border-[#0000FF] bg-blue-50/70 text-[#0000FF] font-bold ring-2 ring-[#0000FF]/30 shadow-2xs' : 'border-neutral-200 bg-white text-neutral-800 hover:border-neutral-400 font-medium shadow-2xs'"
                                    class="p-3.5 text-left border rounded-xl text-xs transition duration-150 cursor-pointer active:scale-[0.98] flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-xs">{{ $m->model_name }}</p>
                                    @if($m->description)
                                        <p class="text-[10px] mt-0.5" :class="selectedModelId === {{ $m->id }} ? 'text-[#0000FF]/70' : 'text-neutral-500'">{{ $m->description }}</p>
                                    @endif
                                </div>
                                <i class="fa-solid fa-circle-check text-xs mt-0.5 text-[#0000FF]" x-show="selectedModelId === {{ $m->id }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Pemilihan Varian Warna / Colorway -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <label class="font-bold uppercase tracking-wider text-neutral-900">2. Pilih Colorway / Warna *</label>
                        <span class="text-neutral-500 font-medium" x-text="selectedColorName ? 'Terpilih: ' + selectedColorName : 'Wajib dipilih'"></span>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($product->colors as $c)
                            <button type="button" 
                                    @click="selectColor({{ $c->id }}, {!! json_encode($c->color_name) !!}, '{{ $c->image_url ?? '' }}')"
                                    :class="selectedColorId === {{ $c->id }} ? 'border-[#0000FF] ring-2 ring-[#0000FF]/30 bg-blue-50/70 text-[#0000FF] font-bold shadow-2xs' : 'border-neutral-200 bg-white text-neutral-800 hover:border-neutral-400 font-medium shadow-2xs'"
                                    class="flex items-center gap-2.5 px-4 py-2.5 border rounded-xl text-xs transition-all duration-150 cursor-pointer active:scale-[0.98]">
                                <span class="w-4 h-4 rounded-full border border-neutral-300 shrink-0 ring-1 ring-white shadow-2xs" style="background-color: {{ $c->hex_code }}"></span>
                                <span>{{ $c->color_name }}</span>
                                <i class="fa-solid fa-check text-[10px] ml-1 text-[#0000FF]" x-show="selectedColorId === {{ $c->id }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Pemilihan Varian Ukuran (Size Sepatu) -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <label class="font-bold uppercase tracking-wider text-neutral-900">3. Pilih Ukuran Sepatu (Size EU) *</label>
                        <span class="text-neutral-500 font-medium" x-text="selectedSizeLabel ? 'Terpilih: ' + selectedSizeLabel : 'Wajib dipilih'"></span>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($product->sizes as $s)
                            <button type="button" 
                                    @click="selectSize({{ $s->id }}, {!! json_encode($s->size_label) !!})"
                                    :class="selectedSizeId === {{ $s->id }} ? 'border-[#0000FF] bg-blue-50/70 text-[#0000FF] font-bold ring-2 ring-[#0000FF]/30 shadow-2xs' : 'border-neutral-200 bg-white text-neutral-800 hover:border-neutral-400 font-semibold shadow-2xs'"
                                    class="min-w-16 px-4 py-2.5 text-center border rounded-xl text-xs uppercase transition duration-150 cursor-pointer active:scale-[0.98]">
                                {{ $s->size_label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Jumlah & Aturan Bonus Barang -->
                <div class="pt-4 border-t border-neutral-200 space-y-4">
                    <div class="flex items-center gap-6">
                        <div class="space-y-1">
                            <label class="text-xs uppercase font-bold text-neutral-900 tracking-wider">Kuantitas Pasang</label>
                            <div class="flex items-center border border-neutral-200 rounded-xl overflow-hidden bg-white">
                                <button type="button" @click="quantity > 1 ? quantity-- : null; recalculatePrice()" class="px-3.5 py-2 text-neutral-500 hover:text-black transition">-</button>
                                <input type="number" x-model="quantity" @change="recalculatePrice()" min="1" class="w-14 text-center text-xs font-semibold focus:outline-none border-none">
                                <button type="button" @click="quantity++; recalculatePrice()" class="px-3.5 py-2 text-neutral-500 hover:text-black transition">+</button>
                            </div>
                        </div>

                        <!-- Informasi Bonus Otomatis -->
                        @if($product->bonuses->where('is_active', true)->count() > 0)
                            @php $bonus = $product->bonuses->where('is_active', true)->first(); @endphp
                            <div class="flex-1 bg-neutral-50 border border-neutral-200 rounded-xl p-3 text-xs text-neutral-900" x-show="quantity >= {{ $bonus->min_quantity }}">
                                <p class="font-bold flex items-center gap-1.5 text-neutral-950">
                                    <i class="fa-solid fa-gift text-[#0000FF]"></i> Selamat! Anda Mendapatkan Bonus:
                                </p>
                                <p class="text-[11px] text-neutral-600 mt-0.5">{{ $bonus->free_item_name }} ({{ $bonus->free_item_description }})</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi Utama (Adem & Lembut) -->
                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit" 
                            :disabled="!isReadyToBuy"
                            :class="isReadyToBuy ? 'bg-[#0000FF] hover:bg-blue-700 text-white cursor-pointer shadow-xs hover:shadow-md active:scale-[0.98]' : 'bg-neutral-100 text-neutral-400 border border-neutral-200 cursor-not-allowed shadow-none'"
                            class="flex-1 py-4 text-xs uppercase tracking-wider font-bold rounded-xl transition-all duration-200 text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span>Tambah ke Keranjang</span>
                    </button>

                    <!-- Tombol Pesan Langsung via WhatsApp -->
                    @if(isset($floatingWa) && $floatingWa)
                        <a :href="'https://wa.me/{{ $floatingWa->phone }}?text=Halo%20Admin,%20saya%20tertarik%20dengan%20sepatu%20' + encodeURIComponent('{{ $product->name }}') + '%20(SKU:%20{{ $product->sku }})'"
                           target="_blank"
                           class="border border-neutral-300 bg-white text-neutral-950 hover:border-neutral-950 hover:bg-neutral-50 px-6 py-4 text-xs uppercase tracking-widest font-bold rounded-xl shadow-xs hover:shadow-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-base text-emerald-600"></i>
                            <span class="hidden sm:inline">Pesan WA</span>
                        </a>
                    @endif
                </div>

                <template x-if="!isReadyToBuy">
                    <p class="text-[11px] text-red-500 font-medium">* Mohon lengkapi pilihan Edisi Box, Colorway, dan Ukuran Sepatu sebelum melanjutkan ke keranjang.</p>
                </template>
            </form>

            <!-- Deskripsi Tab -->
            <div class="pt-8 border-t border-neutral-200 space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-neutral-950">Deskripsi & Spesifikasi Sepatu</h3>
                <div class="text-xs text-neutral-600 leading-relaxed font-normal space-y-2">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
