@extends('layouts.admin')

@section('page_title', '9. Manajemen Kupon Pemasaran')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, couponCode: '', discountType: 'percentage' }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Voucher & Kupon Diskon</h2>
            <p class="text-xs text-slate-500 mt-0.5">Atur kupon diskon persentase/tetap, kuota, target audiens (Semua/Member), dan minimal belanja.</p>
        </div>
        <button @click="addModal = true; generateCode()" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Buat Kupon Baru
        </button>
    </div>

    <!-- Tabel Kupon -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Kode Kupon</th>
                    <th class="p-4">Besaran Diskon</th>
                    <th class="p-4">Target Audiens</th>
                    <th class="p-4">Syarat Belanja</th>
                    <th class="p-4 text-center">Sisa Kuota</th>
                    <th class="p-4 text-center">Tampil Checkout</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($coupons as $c)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4">
                            <span class="font-mono text-sm font-bold text-slate-900 bg-slate-100 px-2.5 py-1 border border-slate-200">
                                {{ $c->code }}
                            </span>
                        </td>
                        <td class="p-4 font-bold text-slate-900">
                            @if($c->discount_type === 'percentage')
                                {{ $c->discount_value }}% (Maks Rp {{ number_format($c->max_discount ?? 0, 0, ',', '.') }})
                            @else
                                Rp {{ number_format($c->discount_value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="p-4">
                            @if($c->target_audience === 'member_only')
                                <span class="bg-blue-50 text-blue-700 font-semibold px-2 py-0.5 border border-blue-200 uppercase text-[10px]">Khusus Member</span>
                            @elseif($c->target_audience === 'non_member_only')
                                <span class="bg-slate-100 text-slate-700 font-semibold px-2 py-0.5 uppercase text-[10px]">Non-Member</span>
                            @else
                                <span class="bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 uppercase text-[10px]">Semua Pembeli</span>
                            @endif
                        </td>
                        <td class="p-4 text-slate-600">
                            Min: Rp {{ number_format($c->min_purchase, 0, ',', '.') }}
                            @if($c->min_quantity > 0)
                                &bull; Min {{ $c->min_quantity }} pcs
                            @endif
                        </td>
                        <td class="p-4 text-center font-bold text-slate-700">
                            {{ is_null($c->quota_total) ? 'Unlimited' : $c->quota_total . ' Kuota' }}
                        </td>
                        <td class="p-4 text-center">
                            @if($c->show_on_checkout)
                                <span class="text-emerald-600 font-bold"><i class="fa-solid fa-check"></i> Ya</span>
                            @else
                                <span class="text-slate-300">Tidak</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus kupon ini?')" class="inline">
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
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada kupon yang dibuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $coupons->links() }}
    </div>

    <!-- Modal Buat Kupon -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-lg w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Buat Kupon Diskon Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="font-semibold text-slate-700">Kode Kupon *</label>
                        <button type="button" @click="generateCode()" class="text-[10px] text-blue-600 font-bold hover:underline">
                            <i class="fa-solid fa-rotate"></i> Generate Otomatis
                        </button>
                    </div>
                    <input type="text" name="code" x-model="couponCode" required placeholder="CONTOH10" class="w-full px-3 py-2 border border-slate-300 font-mono uppercase font-bold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Tipe Diskon *</label>
                        <select name="discount_type" x-model="discountType" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Nilai Diskon *</label>
                        <input type="number" name="discount_value" required value="10" min="1" class="w-full px-3 py-2 border border-slate-300 font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3" x-show="discountType === 'percentage'">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Batas Maksimal Potongan (Rp)</label>
                        <input type="number" name="max_discount" placeholder="50000" class="w-full px-3 py-2 border border-slate-300">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Total Kuota Penggunaan</label>
                        <input type="number" name="quota_total" placeholder="100" class="w-full px-3 py-2 border border-slate-300">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Target Audiens *</label>
                        <select name="target_audience" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                            <option value="all">Semua Pembeli</option>
                            <option value="member_only">Khusus Member Terdaftar</option>
                            <option value="non_member_only">Khusus Non-Member (Pengunjung Baru)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Minimal Belanja (Rp)</label>
                        <input type="number" name="min_purchase" value="150000" min="0" class="w-full px-3 py-2 border border-slate-300">
                    </div>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_on_checkout" value="1" checked class="text-black focus:ring-0">
                        <span class="font-medium text-slate-700">Tampilkan Promo Kupon di Halaman Checkout</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan Kupon</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function generateCode() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let result = 'PASAR';
        for (let i = 0; i < 4; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const el = document.querySelector('[x-data*="couponCode"]');
        if (el && window.Alpine) {
            Alpine.$data(el).couponCode = result;
        } else if (el && el.__x) {
            el.__x.$data.couponCode = result;
        }
    }
</script>
@endsection

