@extends('layouts.admin')

@section('page_title', '15. Admin Handle Order')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Admin Penanggung Jawab Order</h2>
            <p class="text-xs text-slate-500 mt-0.5">Catat kode dan nama personil operasional yang menangani pesanan masuk.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Admin Handle
        </button>
    </div>

    <!-- Tabel Handles -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Kode Admin</th>
                    <th class="p-4">Nama Personil</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($handles as $h)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 font-mono font-bold text-slate-900 text-sm">
                            <span class="bg-slate-100 px-2.5 py-1 border border-slate-200">{{ $h->code }}</span>
                        </td>
                        <td class="p-4 font-bold text-slate-800">{{ $h->name }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold {{ $h->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $h->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.content.handles.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Hapus admin handle ini?')" class="inline">
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
                        <td colspan="4" class="p-8 text-center text-slate-400">Belum ada admin handle terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Handle -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Admin Handle</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.content.handles.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Kode Unik Admin *</label>
                    <input type="text" name="code" required placeholder="Contoh: ADM-01" class="w-full px-3 py-2 border border-slate-300 font-mono uppercase">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Lengkap Penanggung Jawab *</label>
                    <input type="text" name="name" required placeholder="Contoh: Fauzan (Order Ops)" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

