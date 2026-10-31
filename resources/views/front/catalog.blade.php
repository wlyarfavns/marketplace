@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/60 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Katalog Sepatu Branded</span>
        <h1 class="text-3xl font-extrabold text-neutral-950 tracking-tight mt-1">Koleksi PasarKita</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-10">
        
        <!-- Filter Sidebar -->
        <aside class="w-full lg:w-64 shrink-0 space-y-8">
            <form action="{{ route('catalog') }}" method="GET" id="filterForm">
                
                <!-- Search Input -->
                <div class="space-y-3 pb-6 border-b border-neutral-200">
                    <label class="text-xs uppercase font-bold text-neutral-900 tracking-wider">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari model, SKU, atau brand..." 
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 transition">
                    </div>
                </div>

                <!-- Jenis Produk (Ready Stock vs PO) -->
                <div class="space-y-3 py-6 border-b border-neutral-200">
                    <label class="text-xs uppercase font-bold text-neutral-900 tracking-wider">Tipe Produk</label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="" {{ !request('type') ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#0000FF] focus:ring-0">
                            <span class="text-neutral-700">Semua Sepatu</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="ready_stock" {{ request('type') === 'ready_stock' ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#0000FF] focus:ring-0">
                            <span class="text-neutral-700">Ready Stock</span>
                        </label>

                        @if($userType !== 'umum')
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="po" {{ request('type') === 'po' ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#0000FF] focus:ring-0">
                                <span class="font-semibold text-neutral-900">Pre-Order (PO)</span>
                                <span class="bg-blue-50 text-[#0000FF] border border-blue-200 text-[9px] font-bold px-1.5 py-0.2 rounded">MEMBER</span>
                            </label>
                        @else
                            <div class="pt-1 text-[11px] text-neutral-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                                <span>PO khusus <a href="{{ route('login') }}" class="underline text-neutral-900 font-semibold">Member</a></span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="space-y-3 py-6 border-b border-neutral-200">
                    <label class="text-xs uppercase font-bold text-neutral-900 tracking-wider">Brand Sepatu</label>
                    <select name="brand" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:border-neutral-950 transition">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->slug }}" {{ request('brand') === $b->slug ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori Filter -->
                <div class="space-y-3 py-6 border-b border-neutral-200">
                    <label class="text-xs uppercase font-bold text-neutral-900 tracking-wider">Kategori Sepatu</label>
                    <select name="category" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:border-neutral-950 transition">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->slug }}" {{ request('category') === $c->slug ? 'selected' : '' }}>
                                {{ $c->name }} ({{ ucfirst($c->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Urutkan -->
                <div class="space-y-3 py-6 border-b border-neutral-200">
                    <label class="text-xs uppercase font-bold text-neutral-900 tracking-wider">Urutkan</label>
                    <select name="sort" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:border-neutral-950 transition">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                    </select>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="submit" class="w-full bg-[#0000FF] hover:bg-blue-700 text-white text-xs uppercase tracking-widest py-3 font-semibold rounded-xl active:scale-[0.98] transition-all shadow-xs hover:shadow-sm cursor-pointer">
                        Terapkan Filter
                    </button>
                    @if(request()->anyFilled(['q', 'brand', 'category', 'type', 'sort']))
                        <a href="{{ route('catalog') }}" class="px-4 py-3 border border-neutral-300 rounded-xl text-xs font-semibold uppercase tracking-wider text-neutral-700 hover:text-black hover:border-black active:scale-[0.98] transition flex items-center justify-center cursor-pointer" title="Reset">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </aside>

        <!-- Product Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-6 text-xs text-neutral-500">
                <p>Menampilkan <span class="font-bold text-neutral-900">{{ $products->total() }}</span> produk sepatu</p>
                <div class="text-neutral-400">
                    Akses Anda: <span class="uppercase font-bold text-neutral-900">{{ $userType }}</span>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 border border-dashed border-neutral-200 bg-neutral-50/50 rounded-2xl">
                    <i class="fa-solid fa-shoe-prints text-3xl text-neutral-300 mb-3 block"></i>
                    <p class="text-sm font-semibold text-neutral-700">Tidak ada koleksi sepatu yang sesuai dengan filter.</p>
                    <p class="text-xs text-neutral-400 mt-1">Coba gunakan kata kunci lain atau reset filter pencarian Anda.</p>
                    <a href="{{ route('catalog') }}" class="inline-block mt-4 text-xs font-semibold uppercase tracking-wider px-6 py-2.5 bg-neutral-50 hover:bg-[#0000FF] text-neutral-800 hover:text-white border border-neutral-300 rounded-xl transition shadow-2xs">Reset Filter</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                    @foreach($products as $prod)
                        <div class="group relative flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white hover:border-neutral-350 hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div>
                                <!-- Thumbnail Box (Pure White Seamless Studio) -->
                                <div class="relative aspect-[4/3] bg-[#FAFAFA] p-4 sm:p-5 flex items-center justify-center overflow-hidden border-b border-neutral-100">
                                    @if($prod->primary_image_url)
                                        <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" 
                                             onerror="if (this.src !== '{{ $prod->primary_image_url }}') this.src = '{{ $prod->primary_image_url }}';"
                                             class="w-full h-full object-contain group-hover:scale-105 group-hover:-translate-y-1 transition-all duration-500 filter drop-shadow-xs">
                                    @else
                                        <i class="fa-solid fa-shoe-prints text-neutral-300 text-4xl group-hover:scale-110 transition duration-300"></i>
                                    @endif
                                    
                                    <!-- Badges Adem & Soft -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                                        @if($prod->product_type === 'ready_stock')
                                            <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase font-semibold tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#0000FF]"></span>
                                                Ready Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase font-semibold tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                                                <i class="fa-regular fa-clock text-[9px] text-neutral-400"></i>
                                                Pre-Order
                                            </span>
                                        @endif

                                        @if($prod->visible_to_distributor && !$prod->visible_to_umum && !$prod->visible_to_member)
                                            <span class="bg-neutral-100 text-neutral-700 text-[9px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-md border border-neutral-200">
                                                Distributor
                                            </span>
                                        @endif
                                    </div>

                                    <span class="absolute top-3 right-3 inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs text-neutral-600 text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs z-10">
                                        <i class="fa-solid fa-check text-[9px] text-[#0000FF]"></i> 100% Ori
                                    </span>

                                    <a href="{{ route('product.detail', $prod->slug) }}" class="absolute inset-0 z-0" aria-label="Lihat Detail {{ $prod->name }}"></a>
                                </div>

                                <!-- Product Content -->
                                <div class="p-5">
                                    <div class="flex items-center justify-between text-[11px] font-bold tracking-wider uppercase text-neutral-400 mb-1.5">
                                        <span>{{ $prod->brand ? $prod->brand->name : 'PasarKita' }}</span>
                                        <span class="text-neutral-400 font-medium text-[10px] normal-case capitalize">{{ $prod->categories->first()?->name ?? 'Sneakers' }}</span>
                                    </div>

                                    <h3 class="font-bold text-sm sm:text-[15px] text-neutral-900 group-hover:text-[#0000FF] transition-colors line-clamp-1 leading-snug tracking-tight">
                                        <a href="{{ route('product.detail', $prod->slug) }}">{{ $prod->name }}</a>
                                    </h3>

                                    <!-- Rentang Harga & Colorway -->
                                    <div class="mt-3.5 pt-3 border-t border-neutral-100 flex items-center justify-between">
                                        <div>
                                            <span class="text-[9px] uppercase tracking-wider font-semibold text-neutral-400 block">Harga</span>
                                            <span class="text-base font-extrabold text-neutral-950 tracking-tight">
                                                @if($prod->min_price == $prod->max_price)
                                                    Rp {{ number_format($prod->min_price, 0, ',', '.') }}
                                                @else
                                                    Rp {{ number_format($prod->min_price, 0, ',', '.') }} – Rp {{ number_format($prod->max_price, 0, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>

                                        @if($prod->colors->count() > 0)
                                            <div class="flex items-center gap-1.5 pb-0.5">
                                                @foreach($prod->colors->take(3) as $col)
                                                    <span class="w-3.5 h-3.5 rounded-full border border-neutral-200 shadow-2xs" style="background-color: {{ $col->hex_code }}" title="{{ $col->color_name }}"></span>
                                                @endforeach
                                                @if($prod->colors->count() > 3)
                                                    <span class="text-[10px] font-bold text-neutral-400">+{{ $prod->colors->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Detail Button (Adem, Soft, Tactile) -->
                            <div class="p-5 pt-0">
                                <a href="{{ route('product.detail', $prod->slug) }}" 
                                   class="group/btn w-full text-center py-2.5 sm:py-3 bg-neutral-50 hover:bg-[#0000FF] text-neutral-700 hover:text-white border border-neutral-200/90 hover:border-[#0000FF] rounded-xl active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 text-xs font-semibold tracking-wide shadow-2xs hover:shadow-sm">
                                    <span>Pilih Ukuran & Beli</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] text-neutral-400 group-hover/btn:text-white group-hover/btn:translate-x-0.5 transition-all"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
