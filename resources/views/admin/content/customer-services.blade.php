@extends('layouts.admin')

@section('page_title', '12. Customer Service & Floating WhatsApp')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Kontak Customer Service (CS)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola tim CS dan tentukan tepat 1 nomor aktif yang menjadi tombol Floating WhatsApp di seluruh website.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Kontak CS
        </button>
    </div>

    <!-- Tabel CS -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Nama Petugas CS</th>
                    <th class="p-4">Nomor WhatsApp</th>
                    <th class="p-4 text-center">Status Floating WhatsApp</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($services as $s)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 font-bold text-slate-900 text-sm">{{ $s->name }}</td>
                        <td class="p-4 font-mono font-bold text-slate-700">{{ $s->phone }}</td>
                        <td class="p-4 text-center">
                            @if($s->is_floating_whatsapp)
                                <span class="bg-emerald-600 text-white font-bold px-3 py-1 text-[10px] uppercase shadow-xs">
                                    <i class="fa-brands fa-whatsapp mr-1"></i> Floating Aktif Utama
                                </span>
                            @else
                                <form action="{{ route('admin.content.customer-services.set-floating', $s->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-slate-600 hover:text-black border border-slate-300 px-2.5 py-1 uppercase font-semibold">
                                        Set Jadi Floating WA
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold {{ $s->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.content.customer-services.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus CS ini?')" class="inline">
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
                        <td colspan="5" class="p-8 text-center text-slate-400">Belum ada nomor CS terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah CS -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Kontak CS</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.content.customer-services.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Petugas CS *</label>
                    <input type="text" name="name" required placeholder="Contoh: CS Siti (Order & Kemitraan)" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nomor WhatsApp (Gunakan Format 628xxx) *</label>
                    <input type="text" name="phone" required placeholder="6281234567890" class="w-full px-3 py-2 border border-slate-300 font-mono">
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_floating_whatsapp" value="1" class="text-black focus:ring-0">
                        <span class="font-medium text-slate-700">Jadikan Floating WA Utama di Layar Website</span>
                    </label>
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

