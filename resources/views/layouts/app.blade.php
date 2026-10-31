<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PasarKita — Toko Sepatu Branded & Sneakers Original' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    
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
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        primary: '#000000',
                        secondary: '#171717',
                        surface: '#FFFFFF',
                        border: '#E5E7EB',
                        brandBlue: '#0000FF',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #FFFFFF; color: #000000; }
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-white text-black min-h-screen flex flex-col antialiased selection:bg-[#0000FF] selection:text-white" x-data="cartDrawerState()">

    <!-- 1. Top Bar (Dominan Putih & Hitam Minimalis) -->
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
                        <a href="{{ route('account.dashboard') }}" class="font-bold text-black hover:underline">
                            {{ Auth::user()->name }}
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 ml-1 bg-black text-white rounded">
                                {{ Auth::user()->customer_type }}
                            </span>
                        </a>
                    </div>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-xs bg-black text-white font-bold px-2.5 py-0.5 hover:bg-gray-800 transition">Panel Admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-black font-medium">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-black hover:underline transition font-semibold text-gray-800">Masuk Member</a>
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
                <span class="text-2xl sm:text-3xl font-serif tracking-tight font-black text-black uppercase">
                    Pasar<span class="font-light italic text-gray-500">Kita</span>
                </span>
                <span class="hidden sm:inline-block text-[10px] uppercase tracking-widest font-black text-black border-l-2 border-gray-300 pl-3">
                    Sneakers & Shoes
                </span>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide">
                <a href="{{ route('home') }}" class="hover:text-black transition {{ request()->routeIs('home') ? 'text-black border-b-2 border-[#0000FF] pb-1 font-bold' : 'text-gray-600' }}">Beranda</a>
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" class="hover:text-black transition {{ request()->get('type') === 'ready_stock' ? 'text-black border-b-2 border-[#0000FF] pb-1 font-bold' : 'text-gray-600' }}">Ready Stock</a>
                
                @auth
                    @if(in_array(Auth::user()->customer_type, ['member', 'distributor']) || Auth::user()->role === 'admin')
                        <a href="{{ route('catalog', ['type' => 'po']) }}" class="flex items-center gap-1.5 hover:text-black transition {{ request()->get('type') === 'po' ? 'text-black border-b-2 border-[#0000FF] pb-1 font-bold' : 'text-gray-600' }}">
                            <span>Pre-Order (PO)</span>
                            <span class="bg-black text-white text-[10px] font-bold px-1.5 py-0.5 rounded uppercase">Eksklusif</span>
                        </a>
                    @endif
                @else
                    <span class="text-gray-400 cursor-not-allowed text-xs flex items-center gap-1" title="Login untuk membuka katalog PO sepatu">
                        <i class="fa-solid fa-lock text-[10px]"></i> PO (Member Only)
                    </span>
                @endauth

                <a href="{{ route('catalog') }}" class="hover:text-black transition {{ request()->routeIs('catalog') && !request()->has('type') ? 'text-black border-b-2 border-[#0000FF] pb-1 font-bold' : 'text-gray-600' }}">Semua Koleksi</a>
            </nav>

            <!-- Actions (Search, Cart Drawer Trigger, My Account) -->
            <div class="flex items-center gap-5">
                <form action="{{ route('catalog') }}" method="GET" class="hidden lg:flex items-center relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari sepatu, Nike, Jordan, Samba..." 
                        class="text-xs bg-gray-50 border border-gray-300 rounded-none px-3.5 py-2 pr-8 w-64 focus:outline-none focus:border-black transition">
                    <button type="submit" class="absolute right-2.5 text-gray-400 hover:text-black">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </button>
                </form>

                <!-- Cart Button -->
                <button @click="openDrawer()" class="relative p-2 text-gray-800 hover:text-black transition cursor-pointer" title="Keranjang Belanja">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-[#0000FF] text-white text-[10px] font-black w-4.5 h-4.5 rounded-full flex items-center justify-center shadow-md ring-2 ring-white"
                          x-text="cartCount">
                        {{ count(session('cart', [])) }}
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
                        <a href="{{ route('cart.index') }}" class="text-center py-3 bg-white border-2 border-black rounded-lg text-xs font-black uppercase tracking-wider text-black hover:bg-black hover:text-white transition shadow-sm hover:shadow-md active:translate-y-0.5 cursor-pointer">
                            Lihat Keranjang
                        </a>
                        <a href="{{ route('checkout.index') }}" class="text-center py-3 bg-black border-2 border-black text-white text-xs font-black uppercase tracking-wider rounded-lg hover:bg-neutral-900 hover:border-[#0000FF] transition shadow-md hover:shadow-lg active:translate-y-0.5 cursor-pointer">
                            Checkout
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 6. Floating WhatsApp Button (Hitam & Putih) -->
    @if(isset($floatingWa) && $floatingWa)
        <a href="https://wa.me/{{ $floatingWa->phone }}?text=Halo%20Admin%20PasarKita,%20saya%20ingin%20bertanya%20mengenai%20koleksi%20sepatu%20branded..." 
           target="_blank" 
           class="fixed bottom-6 right-6 z-40 bg-black hover:bg-gray-800 text-white border-2 border-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl hover:scale-105 transition duration-200"
           title="Chat WhatsApp CS {{ $floatingWa->name }}">
            <i class="fa-brands fa-whatsapp text-3xl"></i>
        </a>
    @endif

    <!-- 7. Footer (Dominan Putih Bersih dengan Detail Hitam) -->
    <footer class="border-t border-gray-200 bg-white text-gray-600 pt-16 pb-12 mt-20 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Col 1: Brand Info -->
            <div class="space-y-4">
                <span class="text-2xl font-serif font-black text-black uppercase tracking-tight">Pasar<span class="font-light italic text-gray-500">Kita</span></span>
                <p class="text-gray-500 leading-relaxed text-xs">
                    Toko sepatu branded dan sneakers original terpercaya milik owner PasarKita. Menghadirkan rilisan resmi dari Nike, Air Jordan, Adidas, New Balance, Puma, dan Asics dengan garansi keaslian 100% dan kemudahan Pre-Order ber-DP.
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
                    <li><a href="{{ route('login') }}" class="hover:text-black hover:underline transition">Program Mitra Reseller Sepatu</a></li>
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
            <p>&copy; 2026 PasarKita. Seluruh hak cipta dilindungi undang-undang.</p>
            <p>Toko Sepatu Branded Original &bull; Tema Dominan Putih & Hitam</p>
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
