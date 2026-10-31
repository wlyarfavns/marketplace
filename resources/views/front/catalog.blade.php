@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <span class="text-xs uppercase tracking-widest text-black font-bold">Katalog Sepatu Branded</span>
        <h1 class="text-3xl font-serif font-bold text-gray-950 mt-1">Koleksi PasarKita</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-10">
        
        <!-- Filter Sidebar -->
        <aside class="w-full lg:w-64 shrink-0 space-y-8">
            <form action="{{ route('catalog') }}" method="GET" id="filterForm">
                
                <!-- Search Input -->
                <div class="space-y-3 pb-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari model, SKU, atau brand..." 
                            class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                    </div>
                </div>

                <!-- Jenis Produk (Ready Stock vs PO) -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Tipe Produk</label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="" {{ !request('type') ? 'checked' : '' }} onchange="this.form.submit()" class="text-black focus:ring-0">
                            <span>Semua Sepatu</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="ready_stock" {{ request('type') === 'ready_stock' ? 'checked' : '' }} onchange="this.form.submit()" class="text-black focus:ring-0">
                            <span>Ready Stock</span>
                        </label>

                        @if($userType !== 'umum')
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="po" {{ request('type') === 'po' ? 'checked' : '' }} onchange="this.form.submit()" class="text-black focus:ring-0">
                                <span class="font-semibold text-gray-900">Pre-Order (PO)</span>
                                <span class="bg-black text-[9px] font-bold px-1.5 py-0.2 rounded text-white">MEMBER</span>
                            </label>
                        @else
                            <div class="pt-1 text-[11px] text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                                <span>PO hanya untuk <a href="{{ route('login') }}" class="underline text-black font-semibold">Member</a></span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Brand Sepatu</label>
                    <select name="brand" onchange="this.form.submit()" class="w-full text-xs px-3 py-2.5 border border-gray-300 bg-white focus:outline-none focus:border-black">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->slug }}" {{ request('brand') === $b->slug ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori Filter -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Kategori Sepatu</label>
                    <select name="category" onchange="this.form.submit()" class="w-full text-xs px-3 py-2.5 border border-gray-300 bg-white focus:outline-none focus:border-black">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->slug }}" {{ request('category') === $c->slug ? 'selected' : '' }}>
                                {{ $c->name }} ({{ ucfirst($c->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Urutkan -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Urutkan</label>
                    <select name="sort" onchange="this.form.submit()" class="w-full text-xs px-3 py-2.5 border border-gray-300 bg-white focus:outline-none focus:border-black">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                    </select>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="submit" class="w-full bg-black text-white text-xs uppercase tracking-widest py-3 font-bold rounded-lg border-2 border-black hover:bg-neutral-800 hover:border-[#0000FF] shadow-sm hover:shadow-md active:translate-y-0.5 transition cursor-pointer">
                        Terapkan Filter
                    </button>
                    @if(request()->anyFilled(['q', 'brand', 'category', 'type', 'sort']))
                        <a href="{{ route('catalog') }}" class="px-4 py-3 border-2 border-gray-300 rounded-lg text-xs font-bold uppercase tracking-wider text-gray-700 hover:text-black hover:border-black shadow-xs hover:shadow-sm active:translate-y-0.5 transition flex items-center justify-center cursor-pointer" title="Reset">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </aside>

        <!-- Product Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-6 text-xs text-gray-500">
                <p>Menampilkan <span class="font-bold text-gray-900">{{ $products->total() }}</span> produk sepatu</p>
                <div class="text-gray-400">
                    Akses Anda: <span class="uppercase font-semibold text-black">{{ $userType }}</span>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 border border-dashed border-gray-200 bg-gray-50">
                    <i class="fa-solid fa-shoe-prints text-3xl text-gray-300 mb-3 block"></i>
                    <p class="text-sm font-medium text-gray-700">Tidak ada koleksi sepatu yang sesuai dengan filter.</p>
                    <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci lain atau reset filter pencarian Anda.</p>
                    <a href="{{ route('catalog') }}" class="inline-block mt-4 text-xs font-bold uppercase tracking-wider px-6 py-2.5 bg-black text-white rounded-lg border-2 border-black hover:bg-[#0000FF] hover:border-[#0000FF] shadow-sm hover:shadow-md transition">Reset Filter</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($products as $prod)
                        <div class="group relative flex flex-col justify-between rounded-2xl border-2 border-gray-200 bg-white hover:border-black hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <div>
                                <!-- Thumbnail Box -->
                                <div class="relative aspect-[4/3] bg-gradient-to-b from-neutral-50/80 to-neutral-100/40 p-4 sm:p-5 flex items-center justify-center overflow-hidden border-b border-gray-100">
                                    @if($prod->primary_image_url)
                                        <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" 
                                             onerror="if (this.src !== '{{ $prod->primary_image_url }}') this.src = '{{ $prod->primary_image_url }}';"
                                             class="w-full h-full object-contain group-hover:scale-110 group-hover:-translate-y-1 transition-all duration-500 filter drop-shadow-sm">
                                    @else
                                        <i class="fa-solid fa-shoe-prints text-gray-300 text-4xl group-hover:scale-110 transition duration-300"></i>
                                    @endif
                                    
                                    <!-- Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                                        @if($prod->product_type === 'ready_stock')
                                            <span class="inline-flex items-center gap-1.5 bg-black/90 backdrop-blur-xs text-white text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#0000FF] animate-pulse"></span>
                                                Ready Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-neutral-900/90 backdrop-blur-xs text-white text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full border border-neutral-700 shadow-sm">
                                                <i class="fa-solid fa-clock text-[9px] text-amber-400"></i>
                                                Pre-Order (PO)
                                            </span>
                                        @endif

                                        @if($prod->visible_to_distributor && !$prod->visible_to_umum && !$prod->visible_to_member)
                                            <span class="bg-gray-900 text-white text-[9px] uppercase tracking-wider font-bold px-2 py-0.5 rounded shadow-xs">
                                                Khusus Distributor
                                            </span>
                                        @endif
                                    </div>

                                    <span class="absolute top-3 right-3 inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs text-neutral-800 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full border border-gray-200 shadow-xs z-10">
                                        <i class="fa-solid fa-check text-[9px] text-[#0000FF]"></i> 100% Ori
                                    </span>

                                    <a href="{{ route('product.detail', $prod->slug) }}" class="absolute inset-0 z-0" aria-label="Lihat Detail {{ $prod->name }}"></a>
                                </div>

                                <!-- Product Content -->
                                <div class="p-5">
                                    <div class="flex items-center justify-between text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">
                                        <span>{{ $prod->brand ? $prod->brand->name : 'PasarKita' }}</span>
                                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Terverifikasi</span>
                                    </div>

                                    <h3 class="font-bold text-sm sm:text-base text-gray-950 group-hover:text-[#0000FF] transition-colors line-clamp-1 leading-snug">
                                        <a href="{{ route('product.detail', $prod->slug) }}">{{ $prod->name }}</a>
                                    </h3>

                                    <!-- Rating & Category -->
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                        <div class="flex items-center text-amber-400 text-[10px]">
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-700">5.0</span>
                                        <span class="text-gray-300">&bull;</span>
                                        <span class="text-[11px] text-gray-500 truncate">{{ $prod->categories->first()?->name ?? 'Sneakers' }}</span>
                                    </div>

                                    <!-- Rentang Harga & Colorway -->
                                    <div class="mt-3.5 pt-3 border-t border-gray-100 flex items-end justify-between">
                                        <div>
                                            <span class="text-[9px] uppercase tracking-wider font-bold text-gray-400 block">Harga Resmi</span>
                                            <span class="text-base font-black text-gray-950 tracking-tight">
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
                                                    <span class="w-3.5 h-3.5 rounded-full border border-gray-300 shadow-xs ring-1 ring-white" style="background-color: {{ $col->hex_code }}" title="{{ $col->color_name }}"></span>
                                                @endforeach
                                                @if($prod->colors->count() > 3)
                                                    <span class="text-[10px] font-bold text-gray-400">+{{ $prod->colors->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Detail -->
                            <div class="p-5 pt-0">
                                <a href="{{ route('product.detail', $prod->slug) }}" 
                                   class="w-full text-center py-3.5 bg-black border-2 border-black text-white hover:bg-neutral-900 hover:border-[#0000FF] text-xs uppercase tracking-widest font-black rounded-xl shadow-md hover:shadow-lg active:translate-y-0.5 transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                                    <span>Pilih Ukuran & Beli</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
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
