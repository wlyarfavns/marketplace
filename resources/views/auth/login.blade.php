@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border border-neutral-200/90 rounded-3xl p-8 sm:p-10 bg-white space-y-6 shadow-xs">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Portal Pelanggan</span>
            <h1 class="text-2xl font-extrabold text-neutral-950 tracking-tight">Masuk ke Akun Anda</h1>
            <p class="text-xs text-neutral-500 font-normal">Akses katalog Pre-Order eksklusif, buku alamat, dan riwayat pesanan.</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-bold text-neutral-900">Alamat Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@email.com"
                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                @error('email')
                    <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-neutral-900">Kata Sandi *</label>
                </div>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-neutral-600">
                    <input type="checkbox" name="remember" class="text-neutral-950 focus:ring-0 rounded">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-[#0000FF] hover:bg-blue-700 text-white text-xs uppercase tracking-widest font-bold rounded-xl active:scale-[0.98] transition shadow-xs hover:shadow-md cursor-pointer">
                Masuk ke Akun
            </button>
        </form>

        <div class="p-4 bg-neutral-50 border border-neutral-200 rounded-2xl text-xs space-y-2">
            <p class="font-bold text-neutral-900 uppercase tracking-wide text-[10px]">Akun Demo Tersedia:</p>
            <div class="space-y-1 text-[11px] text-neutral-600">
                <p><span class="font-semibold text-neutral-900">Member:</span> member@pasarkita.com (pass: password)</p>
                <p><span class="font-semibold text-neutral-900">Distributor:</span> distributor@pasarkita.com (pass: password)</p>
                <p><span class="font-semibold text-neutral-900">Customer Umum:</span> umum@pasarkita.com (pass: password)</p>
            </div>
        </div>

        <p class="text-xs text-center text-neutral-500">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-neutral-950 hover:underline">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
