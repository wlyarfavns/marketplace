<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel Administrator — FOOTWORK' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
                    },
                    colors: {
                        adminDark: '#000000',
                        adminBorder: '#E2E8F0',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; color: #1E293B; }
    </style>
</head>
<body class="min-h-screen flex antialiased" x-data="{ sidebarOpen: false }">

    <!-- 1. Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-200 transform lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div>
            <!-- Admin Header -->
            <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <span class="font-bold text-lg tracking-tight text-slate-900 uppercase">FOOTWORK <span class="text-xs font-normal text-slate-400">Admin</span></span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Navigation Links (16 Modules) -->
            <nav class="p-4 space-y-1 overflow-y-auto max-h-[calc(100vh-8rem)] text-xs font-medium">
                <div class="text-[10px] uppercase font-bold text-slate-400 px-3 py-1.5 tracking-wider">Ringkasan & Penjualan</div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.dashboard') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-chart-pie w-4 text-center"></i>
                    <span>1. Dashboard & DP</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.orders.*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-receipt w-4 text-center"></i>
                    <span>2. Kelola Pesanan</span>
                </a>

                <a href="{{ route('admin.sellers.report') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.sellers.report*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-4 text-center"></i>
                    <span>3. Laporan Penjualan Mitra</span>
                </a>

                <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 py-1.5 tracking-wider">Katalog Produk & Brand</div>

                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.products.*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-shoe-prints w-4 text-center"></i>
                    <span>4. Produk & Matriks Harga</span>
                </a>

                <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.brands.*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-tags w-4 text-center"></i>
                    <span>5. Manajemen Brand Sepatu</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.categories.*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-layer-group w-4 text-center"></i>
                    <span>6. Kategori Sepatu</span>
                </a>

                <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 py-1.5 tracking-wider">Kemitraan & Pengguna</div>

                <a href="{{ route('admin.sellers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.sellers.index') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-handshake w-4 text-center"></i>
                    <span>7. Mitra Seller (ID Seller)</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.users.*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-users w-4 text-center"></i>
                    <span>8. Pengguna & Customer Tier</span>
                </a>

                <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.coupons.*') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-ticket w-4 text-center"></i>
                    <span>9. Kupon Pemasaran</span>
                </a>

                <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 py-1.5 tracking-wider">Konten & Pengaturan Toko</div>

                <a href="{{ route('admin.content.banners') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.content.banners') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-images w-4 text-center"></i>
                    <span>10. Banner Promo (21:7)</span>
                </a>

                <a href="{{ route('admin.content.features') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.content.features') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-star w-4 text-center"></i>
                    <span>11. Keunggulan Toko</span>
                </a>

                <a href="{{ route('admin.content.customer-services') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.content.customer-services') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-brands fa-whatsapp w-4 text-center"></i>
                    <span>12. Customer Service & WA</span>
                </a>

                <a href="{{ route('admin.content.banks') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.content.banks') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-building-columns w-4 text-center"></i>
                    <span>13. Rekening Bank Transfer</span>
                </a>

                <a href="{{ route('admin.content.socials') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.content.socials') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-share-nodes w-4 text-center"></i>
                    <span>14. Media Sosial</span>
                </a>

                <a href="{{ route('admin.content.handles') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.content.handles') ? 'bg-black text-white font-semibold' : '' }}">
                    <i class="fa-solid fa-id-badge w-4 text-center"></i>
                    <span>15. Admin Handle Order</span>
                </a>

                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center"></i>
                    <span>16. Lihat Front-Store Website</span>
                </a>
            </nav>
        </div>

        <!-- Admin Profile & Logout -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-black text-white text-xs flex items-center justify-center font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="text-xs">
                    <p class="font-semibold text-slate-900 truncate w-32">{{ Auth::user()->name }}</p>
                    <p class="text-slate-400 text-[10px]">Super Administrator</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-600 p-1.5" title="Keluar">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- 2. Main Content Wrapper -->
    <div class="flex-1 lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <h1 class="text-sm font-semibold text-slate-800">@yield('page_title', 'Dashboard Back-Office')</h1>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank" class="text-xs border border-slate-200 px-3 py-1.5 rounded text-slate-600 hover:bg-slate-50 flex items-center gap-1.5">
                    <i class="fa-solid fa-store"></i> Toko Utama
                </a>
            </div>
        </header>

        <!-- Flash Alert -->
        <div class="p-6 pb-0">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 rounded mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800">&times;</button>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="p-6 flex-1">
            @yield('content')
        </main>
    </div>

</body>
</html>

