<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FOOTWORK — Toko Sepatu Branded & Sneakers Original' }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0A0A0A',
                        secondary: '#171717',
                        surface: '#FFFFFF',
                        border: '#E5E7EB',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif; background-color: #FFFFFF; color: #0A0A0A; }
        h1, h2, h3, h4, h5, h6, .font-serif, .font-display { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            letter-spacing: -0.025em; 
        }
    </style>
</head>
<body class="bg-white text-black min-h-screen flex flex-col antialiased selection:bg-neutral-900 selection:text-white" x-data="cartDrawerState()">

    <!-- Admin Alert Bar jika Login sebagai Administrator -->
    @auth
        @if(Auth::user()->role === 'admin')
            <div class="bg-neutral-950 text-white text-xs py-2 px-4 sm:px-8 border-b border-neutral-800">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold">Mode Administrator:</span>
                        <span class="text-neutral-400 hidden sm:inline">Anda sedang melihat tampilan etalase toko.</span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 bg-white text-neutral-950 text-[11px] font-extrabold px-3.5 py-1 rounded-md hover:bg-neutral-100 transition shadow-xs">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Buka Dashboard Panel Admin &rarr;</span>
                    </a>
                </div>
            </div>
        @endif
    @endauth

    <!-- 1. Top Bar Minimalis -->
    <div class="border-b border-gray-200 bg-white text-gray-700 text-xs py-2.5 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-4 text-gray-600">
                <span><i class="fa-solid fa-certificate text-black mr-1.5"></i> 100% Authentic Branded Shoes</span>
                <span class="hidden md:inline">•</span>
                <span class="hidden md:inline"><i class="fa-solid fa-box text-black mr-1.5"></i> Double-Box Packing & Pengiriman Seluruh Indonesia</span>
            </div>
            <div class="flex items-center gap-5 text-gray-700">
                @auth
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Halo,</span>
                        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('account.dashboard') }}" class="font-bold text-black hover:underline flex items-center gap-1">
                            <span>{{ Auth::user()->name }}</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 bg-black text-white rounded">
                                {{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PELANGGAN' }}
                            </span>
                        </a>
                    </div>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-xs bg-black text-white font-bold px-3 py-1 rounded-md hover:bg-neutral-800 transition shadow-2xs flex items-center gap-1">
                            <i class="fa-solid fa-gauge text-[10px]"></i>
                            <span>Panel Admin</span>
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-black font-medium">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-black hover:underline transition font-semibold text-gray-800">Masuk Akun</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('register') }}" class="hover:text-black hover:underline transition font-semibold text-gray-800">Daftar Akun</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- 2. Header & Main Navigation (Putih & Hitam) -->
    <header class="border-b border-gray-200 sticky top-0 bg-white/95 backdrop-blur-md z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 flex items-center justify-between gap-6">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="text-2xl sm:text-[26px] font-extrabold tracking-tighter text-neutral-950 uppercase">
                    FOOTWORK.
                </span>
                <span class="hidden sm:inline-block text-[10px] uppercase tracking-widest font-bold text-neutral-400 border-l border-neutral-200 pl-3">
                    Sneakers & Shoes
                </span>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide">
                <a href="{{ route('home') }}" class="hover:text-black transition {{ request()->routeIs('home') ? 'text-black border-b-2 border-neutral-950 pb-1 font-bold' : 'text-gray-600' }}">Beranda</a>
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" class="hover:text-black transition {{ request()->get('type') === 'ready_stock' ? 'text-black border-b-2 border-neutral-950 pb-1 font-bold' : 'text-gray-600' }}">Ready Stock</a>
                <a href="{{ route('catalog', ['type' => 'po']) }}" class="flex items-center gap-1.5 hover:text-black transition {{ request()->get('type') === 'po' ? 'text-black border-b-2 border-neutral-950 pb-1 font-bold' : 'text-gray-600' }}">
                    <span>Pre-Order (PO)</span>
                </a>
                <a href="{{ route('catalog') }}" class="hover:text-black transition {{ request()->routeIs('catalog') && !request()->has('type') ? 'text-black border-b-2 border-neutral-950 pb-1 font-bold' : 'text-gray-600' }}">Semua Koleksi</a>
            </nav>

            <!-- Actions (Search, Cart Drawer Trigger, My Account) -->
            <div class="flex items-center gap-5">
                <form action="{{ route('catalog') }}" method="GET" class="hidden lg:flex items-center relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari sepatu, Nike, Jordan, Samba..." 
                        class="text-xs bg-neutral-50/80 border border-neutral-200 rounded-full px-4 py-2 pr-9 w-64 focus:outline-none focus:border-neutral-950 focus:bg-white transition-all placeholder:text-neutral-400">
                    <button type="submit" class="absolute right-3 text-neutral-400 hover:text-black transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </button>
                </form>

                <!-- Cart Button -->
                <button @click="openDrawer()" class="relative p-2 text-gray-800 hover:text-black transition cursor-pointer" title="Keranjang Belanja">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span x-show="cartCount > 0" 
                          x-cloak
                          class="absolute -top-0.5 -right-0.5 text-[11px] font-bold text-neutral-950 leading-none"
                          x-text="cartCount">
                        @if(count(session('cart', [])) > 0)
                            {{ count(session('cart', [])) }}
                        @endif
                    </span>
                </button>

                @auth
                    <a href="{{ route('account.dashboard') }}" class="p-2 text-gray-800 hover:text-black transition" title="Akun Saya">
                        <i class="fa-regular fa-user text-lg"></i>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- 3. Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-black text-white text-sm px-4 py-3 border-l-4 border-gray-400 flex justify-between items-center mb-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-white"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-gray-300 hover:text-white">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gray-100 text-black text-sm px-4 py-3 border-l-4 border-black flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-black"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-gray-500 hover:text-black">&times;</button>
            </div>
        @endif
    </div>

    <!-- 4. Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- 5. Slide-Over Cart Drawer -->
    <div x-show="isOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" 
         aria-modal="true">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
             @click="closeDrawer()"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white border-l border-gray-200 flex flex-col justify-between"
                 x-show="isOpen"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <!-- Drawer Header -->
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-white">
                    <h2 class="text-lg font-serif font-bold text-gray-900">Keranjang Belanja (<span x-text="cartCount"></span>)</h2>
                    <button @click="closeDrawer()" class="text-gray-400 hover:text-black">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Drawer Items List -->
                <div class="p-6 overflow-y-auto flex-1 space-y-4 divide-y divide-gray-100">
                    <template x-if="items.length === 0">
                        <div class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-bag-shopping text-3xl mb-3 block text-gray-300"></i>
                            <p class="text-sm">Keranjang Anda masih kosong</p>
                            <a href="{{ route('catalog') }}" @click="closeDrawer()" class="mt-4 inline-block text-xs uppercase tracking-wider font-bold border-b-2 border-black pb-0.5 text-black">Jelajahi Koleksi Sepatu</a>
                        </div>
                    </template>

                    <template x-for="item in items" :key="item.cart_key">
                        <div class="pt-4 first:pt-0 flex gap-4">
                            <div class="w-20 h-24 bg-neutral-100 border border-gray-100 flex items-center justify-center shrink-0 overflow-hidden">
                                <template x-if="item.image">
                                    <img :src="item.image.startsWith('http') ? item.image : (item.image.startsWith('images/') ? '/' + item.image : '/storage/' + item.image)" :alt="item.product_name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.image">
                                    <i class="fa-solid fa-shoe-prints text-gray-300 text-xl"></i>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate" x-text="item.product_name"></h4>
                                    <button @click="removeItem(item.cart_key)" class="text-gray-400 hover:text-red-500 text-xs ml-2">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Varian: <span x-text="item.model_name"></span> / <span x-text="item.color_name"></span> / <span x-text="item.size_name"></span>
                                </p>
                                <template x-if="item.bonus_item">
                                    <span class="inline-block mt-1 bg-gray-100 text-black text-[10px] px-1.5 py-0.5 border border-gray-300 font-bold">
                                        Bonus: <span x-text="item.bonus_item"></span>
                                    </span>
                                </template>
                                <div class="flex justify-between items-center mt-3">
                                    <div class="flex items-center border border-gray-300">
                                        <button @click="updateQty(item.cart_key, item.quantity - 1)" class="px-2.5 py-1 text-xs text-gray-500 hover:text-black">-</button>
                                        <span class="px-2 text-xs font-medium" x-text="item.quantity"></span>
                                        <button @click="updateQty(item.cart_key, item.quantity + 1)" class="px-2.5 py-1 text-xs text-gray-500 hover:text-black">+</button>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900" x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Footer -->
                <div class="p-6 border-t border-gray-100 bg-gray-50 space-y-3">
                    <div class="flex justify-between text-sm font-medium">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900 font-bold text-base" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <p class="text-[11px] text-gray-500">Ongkos kirim dan potongan kupon dihitung saat checkout.</p>
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <a href="{{ route('cart.index') }}" class="text-center py-3 bg-white border border-neutral-300 rounded-xl text-xs font-semibold uppercase tracking-wider text-neutral-800 hover:border-neutral-950 transition shadow-2xs active:scale-[0.98] cursor-pointer">
                            Lihat Keranjang
                        </a>
                        <a href="{{ route('checkout.index') }}" class="text-center py-3 bg-neutral-950 hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-xs hover:shadow-sm active:scale-[0.98] cursor-pointer">
                            Checkout
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 6. Floating WhatsApp Button -->
    @if(isset($floatingWa) && $floatingWa)
        <a href="https://wa.me/{{ $floatingWa->phone }}?text=Halo%20Admin%20FOOTWORK,%20saya%20ingin%20bertanya%20mengenai%20koleksi%20sepatu%20branded..." 
           target="_blank" 
           rel="noopener noreferrer"
           class="fixed bottom-6 right-6 z-50 flex items-center justify-center rounded-full bg-[#25D366] text-white shadow-[0_6px_20px_rgba(37,211,102,0.4)] hover:bg-[#20ba59] hover:shadow-[0_8px_25px_rgba(37,211,102,0.55)] hover:scale-105 active:scale-95 transition-all duration-300 group cursor-pointer"
           style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;"
           title="Chat WhatsApp CS {{ $floatingWa->name }}"
           aria-label="Chat WhatsApp CS {{ $floatingWa->name }}">
            <span class="absolute right-16 px-3 py-1.5 bg-neutral-900 text-white text-[11px] font-semibold rounded-lg shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                Chat CS FOOTWORK
            </span>
            <svg class="w-7 h-7 fill-white translate-x-[0.5px]" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
        </a>
    @endif

    <!-- 7. Footer -->
    <footer class="border-t border-gray-200 bg-white text-gray-600 pt-16 pb-12 mt-20 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Col 1: Brand Info -->
            <div class="space-y-4">
                <span class="text-2xl font-extrabold text-neutral-950 uppercase tracking-tighter">FOOTWORK.</span>
                <p class="text-gray-500 leading-relaxed text-xs">
                    Toko sepatu branded dan sneakers original terpercaya milik FOOTWORK. Menghadirkan rilisan resmi dari Nike, Air Jordan, Adidas, New Balance, Puma, dan Asics dengan garansi keaslian 100% dan kemudahan Pre-Order ber-DP.
                </p>
                <div class="flex items-center gap-3 pt-2 text-sm text-black">
                    <a href="#" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-black hover:text-white transition flex items-center justify-center"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-black hover:text-white transition flex items-center justify-center"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-black hover:text-white transition flex items-center justify-center"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- Col 2: Navigasi Cepat -->
            <div class="space-y-3">
                <h4 class="text-xs uppercase font-black text-black tracking-wider">Katalog Sepatu</h4>
                <ul class="space-y-2 text-gray-500">
                    <li><a href="{{ route('catalog', ['type' => 'ready_stock']) }}" class="hover:text-black hover:underline transition">Ready Stock Sneakers</a></li>
                    <li><a href="{{ route('catalog', ['type' => 'po']) }}" class="hover:text-black hover:underline transition">Pre-Order Eksklusif (PO)</a></li>
                    <li><a href="{{ route('catalog') }}" class="hover:text-black hover:underline transition">Semua Koleksi Sepatu</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-black hover:underline transition">Akses Masuk Pelanggan</a></li>
                </ul>
            </div>

            <!-- Col 3: Layanan & Akun -->
            <div class="space-y-3">
                <h4 class="text-xs uppercase font-black text-black tracking-wider">Layanan Pelanggan</h4>
                <ul class="space-y-2 text-gray-500">
                    <li><a href="{{ route('checkout.index') }}" class="hover:text-black hover:underline transition">Checkout Cepat (Guest)</a></li>
                    <li><a href="{{ route('account.dashboard') }}" class="hover:text-black hover:underline transition">Halaman Akun Saya</a></li>
                    <li><a href="{{ route('account.orders') }}" class="hover:text-black hover:underline transition">Lacak Riwayat Pesanan</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-black hover:underline transition">Portal Administrator</a></li>
                </ul>
            </div>

            <!-- Col 4: Informasi Pembayaran & Ekspedisi -->
            <div class="space-y-3">
                <h4 class="text-xs uppercase font-black text-black tracking-wider">Metode Pembayaran</h4>
                <p class="text-gray-500 text-[11px] leading-relaxed">
                    Mendukung Pembayaran Penuh (100%) dan skema Uang Muka (Down Payment / DP) via Transfer Bank Central Asia (BCA), Bank Mandiri, dan Bank Syariah Indonesia (BSI).
                </p>
                <div class="pt-2 text-gray-500 text-xs">
                    <p class="font-bold text-black">Hubungi Kami:</p>
                    <p>Senin – Sabtu (08:00 – 21:00 WIB)</p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-8 mt-12 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center text-gray-400 text-[11px] gap-2">
            <p>&copy; 2026 FOOTWORK. Seluruh hak cipta dilindungi undang-undang.</p>
        </div>
    </footer>

    <!-- Alpine Store & Cart Drawer Script -->
    <script>
        function cartDrawerState() {
            return {
                isOpen: false,
                cartCount: {{ count(session('cart', [])) }},
                subtotal: {{ array_sum(array_column(session('cart', []), 'subtotal')) }},
                items: {!! json_encode(array_values(session('cart', []))) !!},

                openDrawer() {
                    this.isOpen = true;
                },
                closeDrawer() {
                    this.isOpen = false;
                },
                formatRupiah(number) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
                },
                updateQty(key, newQty) {
                    if (newQty < 1) return;
                    fetch('{{ route("cart.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ cart_key: key, quantity: newQty })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const item = this.items.find(i => i.cart_key === key);
                            if (item) {
                                item.quantity = newQty;
                                item.subtotal = data.item_subtotal;
                            }
                            this.cartCount = data.cart_count;
                            this.subtotal = data.subtotal;
                        }
                    });
                },
                removeItem(key) {
                    fetch('{{ route("cart.remove") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ cart_key: key })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.items = this.items.filter(i => i.cart_key !== key);
                            this.cartCount = data.cart_count;
                            this.subtotal = data.subtotal;
                        }
                    });
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
