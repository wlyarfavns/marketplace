<div class="border-b border-neutral-200 bg-neutral-50/60 mb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <nav class="flex space-x-2 sm:space-x-8 overflow-x-auto py-2 text-xs font-semibold uppercase tracking-wider no-scrollbar">
            <a href="{{ route('account.dashboard') }}" 
               class="px-3 py-2.5 whitespace-nowrap transition border-b-2 {{ request()->routeIs('account.dashboard') ? 'border-neutral-950 text-neutral-950 font-bold' : 'border-transparent text-neutral-500 hover:text-neutral-900' }}">
                <i class="fa-solid fa-chart-pie mr-1.5 text-[11px]"></i> Dasbor
            </a>
            <a href="{{ route('account.orders') }}" 
               class="px-3 py-2.5 whitespace-nowrap transition border-b-2 {{ request()->routeIs('account.orders*') || request()->routeIs('account.order.detail') ? 'border-neutral-950 text-neutral-950 font-bold' : 'border-transparent text-neutral-500 hover:text-neutral-900' }}">
                <i class="fa-solid fa-box-archive mr-1.5 text-[11px]"></i> Riwayat Pemesanan
            </a>
            <a href="{{ route('account.transactions') }}" 
               class="px-3 py-2.5 whitespace-nowrap transition border-b-2 {{ request()->routeIs('account.transactions*') || request()->routeIs('account.transaction.detail') ? 'border-neutral-950 text-neutral-950 font-bold' : 'border-transparent text-neutral-500 hover:text-neutral-900' }}">
                <i class="fa-solid fa-receipt mr-1.5 text-[11px]"></i> Riwayat Transaksi
            </a>
            <a href="{{ route('account.profile') }}" 
               class="px-3 py-2.5 whitespace-nowrap transition border-b-2 {{ request()->routeIs('account.profile*') ? 'border-neutral-950 text-neutral-950 font-bold' : 'border-transparent text-neutral-500 hover:text-neutral-900' }}">
                <i class="fa-solid fa-user-gear mr-1.5 text-[11px]"></i> Profil & Preferensi
            </a>
        </nav>
    </div>
</div>

