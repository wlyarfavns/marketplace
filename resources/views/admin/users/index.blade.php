@extends('layouts.admin')

@section('page_title', '8. Kelola Pengguna (Admin & Pelanggan)')

@section('content')
<div class="space-y-6" x-data="{ editModal: false, resetModal: false, selectedUser: {} }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Manajemen Pengguna & Hak Akses</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola 2 role pengguna sistem: Administrator dan Pelanggan.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama / Email / HP..." 
                       class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
            </div>

            <div>
                <select name="role" class="w-full px-3 py-2 border border-slate-300 bg-white">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Pelanggan</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-slate-900 text-white font-semibold py-2">Filter</button>
                @if(request()->anyFilled(['q', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 border border-slate-300 text-slate-500">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Nama & Email</th>
                    <th class="p-4">Nomor WhatsApp</th>
                    <th class="p-4">Role Pengguna</th>
                    <th class="p-4">Kota / Domisili</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $u->name }}</p>
                            <span class="text-slate-500 text-[11px]">{{ $u->email }}</span>
                        </td>
                        <td class="p-4 font-mono text-slate-700">{{ $u->phone ?? '-' }}</td>
                        <td class="p-4">
                            @if($u->role === 'admin')
                                <span class="bg-slate-900 text-white font-bold px-2.5 py-1 text-[10px] uppercase rounded-xs">Admin</span>
                            @else
                                <span class="bg-slate-100 text-slate-800 font-bold px-2.5 py-1 text-[10px] uppercase border border-slate-200 rounded-xs">Pelanggan</span>
                            @endif
                        </td>
                        <td class="p-4 text-slate-600">{{ $u->city ?? '-' }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold {{ $u->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $u->status }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" 
                                        @click="editModal = true; selectedUser = {{ json_encode($u) }}"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-900 hover:text-white border border-slate-300 text-slate-800 text-[10px] font-semibold uppercase">
                                    Edit Role
                                </button>
                                <button type="button" 
                                        @click="resetModal = true; selectedUser = {{ json_encode($u) }}"
                                        class="px-2.5 py-1 bg-white hover:bg-red-50 border border-slate-300 text-red-600 text-[10px] font-semibold uppercase" title="Reset Password">
                                    <i class="fa-solid fa-key"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data pengguna yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>

    <!-- Modal Edit Role Pengguna -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Ubah Data & Role Pengguna</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form :action="'{{ url('admin/users') }}/' + selectedUser.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Pengguna</label>
                    <input type="text" name="name" x-model="selectedUser.name" required class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" x-model="selectedUser.phone" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Role Pengguna Sistem *</label>
                    <select name="role" x-model="selectedUser.role" required class="w-full px-3 py-2 border border-slate-300 bg-white font-semibold">
                        <option value="customer">Pelanggan (Customer / Pembeli Toko)</option>
                        <option value="admin">Administrator (Akses Penuh Panel Admin)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Status Akun *</label>
                    <select name="status" x-model="selectedUser.status" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div x-show="resetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-sm w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Reset Kata Sandi</h3>
                <button @click="resetModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form :action="'{{ url('admin/users') }}/' + selectedUser.id + '/reset-password'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Kata Sandi Baru *</label>
                    <input type="password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter..." 
                           class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="resetModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-red-600 text-white font-bold uppercase tracking-wider">Reset Sandi</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
