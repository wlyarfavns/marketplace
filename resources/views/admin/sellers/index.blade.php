@extends('layouts.admin')

@section('page_title', '7. Manajemen Mitra Penjual (ID Seller)')

@section('content')
<div class="space-y-6" x-data="{ isModalOpen: false, editMode: false, currentSeller: {} }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Mitra Penjual (Sellers)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kode unik ID Seller per Brand untuk atribusi komisi dan closing penjualan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sellers.report') }}" class="border border-slate-300 text-slate-700 text-xs uppercase font-semibold px-4 py-2.5 hover:bg-slate-50 transition">
                <i class="fa-solid fa-chart-line mr-1"></i> Buka Laporan Performa Mitra
            </a>
            <button @click="isModalOpen = true; editMode = false; currentSeller = {}" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
                + Daftarkan Seller Baru
            </button>
        </div>
    </div>

    <!-- Tabel Mitra Seller -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Kode ID Seller</th>
                    <th class="p-4">Nama Mitra</th>
                    <th class="p-4">Brand Terikat</th>
                    <th class="p-4">WhatsApp</th>
                    <th class="p-4 text-center">Total Closing</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sellers as $s)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4">
                            <span class="font-mono font-bold text-xs bg-slate-100 px-2 py-1 border border-slate-200 text-slate-900">
                                {{ $s->seller_code }}
                            </span>
                        </td>
                        <td class="p-4 font-bold text-slate-900">{{ $s->name }}</td>
                        <td class="p-4 font-semibold text-slate-700">{{ $s->brand ? $s->brand->name : '-' }}</td>
                        <td class="p-4 font-mono text-slate-600">{{ $s->phone ?? '-' }}</td>
                        <td class="p-4 text-center font-bold text-slate-900">{{ $s->orders_count }} Transaksi</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold {{ $s->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700' }}">
                                {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.sellers.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus mitra seller ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada data mitra seller.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $sellers->links() }}</div>

    <!-- Modal Form Tambah Seller -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Daftarkan Mitra Seller Baru</h3>
                <button @click="isModalOpen = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.sellers.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Kode Unik ID Seller *</label>
                    <input type="text" name="seller_code" required placeholder="Contoh: MITRA-BANDUNG01" class="w-full px-3 py-2 border border-slate-300 font-mono uppercase focus:outline-none focus:border-black">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Brand Terikat *</label>
                    <select name="brand_id" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Lengkap Mitra / Agen *</label>
                    <input type="text" name="name" required placeholder="Nama Mitra" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nomor WhatsApp Aktif</label>
                    <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan Mitra</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

