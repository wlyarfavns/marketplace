@extends('layouts.admin')

@section('page_title', '8. Kelola Pengguna & Customer Tier (Umum, Member, Distributor)')

@section('content')
<div class="space-y-6" x-data="{ editModal: false, resetModal: false, selectedUser: {} }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Manajemen Pengguna & Hak Akses</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola role (Admin vs Customer) dan customer tier (Umum, Member, Distributor).</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama / Email / HP..." 
                       class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
            </div>

            <div>
                <select name="role" class="w-full px-3 py-2 border border-slate-300 bg-white">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>

            <div>
                <select name="customer_type" class="w-full px-3 py-2 border border-slate-300 bg-white">
                    <option value="">Semua Tier Pelanggan</option>
                    <option value="umum" {{ request('customer_type') === 'umum' ? 'selected' : '' }}>Umum / Non-Member</option>
                    <option value="member" {{ request('customer_type') === 'member' ? 'selected' : '' }}>Registered Member</option>
                    <option value="distributor" {{ request('customer_type') === 'distributor' ? 'selected' : '' }}>Distributor Grosir (B2B)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-slate-900 text-white font-semibold py-2">Filter</button>
                @if(request()->anyFilled(['q', 'role', 'customer_type']))
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
                    <th class="p-4">Role Sistem</th>
                    <th class="p-4">Customer Tier</th>
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
                                <span class="bg-slate-900 text-white font-bold px-2 py-0.5 text-[10px] uppercase">Admin</span>
                            @else
                                <span class="bg-slate-100 text-slate-700 font-medium px-2 py-0.5 text-[10px] uppercase">Customer</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($u->customer_type === 'distributor')
                                <span class="bg-amber-100 text-amber-900 font-bold px-2.5 py-1 text-[10px] uppercase border border-amber-300">Distributor (B2B)</span>
                            @elseif($u->customer_type === 'member')
                                <span class="bg-blue-50 text-blue-800 font-bold px-2.5 py-1 text-[10px] uppercase border border-blue-200">Member</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 font-medium px-2 py-0.5 text-[10px] uppercase">Umum</span>
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
                                    Edit Tier
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
                        <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada data pengguna yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>

    <!-- Modal Edit Tier & Role -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Ubah Role & Customer Tier</h3>
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
                    <label class="font-semibold text-slate-700">Role Sistem *</label>
                    <select name="role" x-model="selectedUser.role" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                        <option value="customer">Customer (Pelanggan / Pembeli)</option>
                        <option value="admin">Administrator (Akses Penuh Back-Office)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Customer Tier (Tingkatan Hak Akses) *</label>
                    <select name="customer_type" x-model="selectedUser.customer_type" required class="w-full px-3 py-2 border border-slate-300 bg-white font-bold">
                        <option value="umum">Umum (Hanya Ready Stock, Tidak Ada PO)</option>
                        <option value="member">Member (Akses PO & Kupon Khusus Member)</option>
                        <option value="distributor">Distributor (B2B Grosir, Rule Diskon Otomatis, Produk Eksklusif)</option>
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
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Perbarui Pengguna</button>
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

                <p class="text-slate-500 text-[11px]">Setel kata sandi baru untuk <strong x-text="selectedUser.name"></strong>:</p>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Kata Sandi Baru (Min 6 Karakter) *</label>
                    <input type="password" name="new_password" required minlength="6" placeholder="••••••••" class="w-full px-3 py-2 border border-slate-300">
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

