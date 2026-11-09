@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border border-neutral-200/90 rounded-3xl p-8 sm:p-10 bg-white space-y-6 shadow-xs">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-neutral-100 flex items-center justify-center text-neutral-900">
                <i class="fa-solid fa-key text-lg"></i>
            </div>
            <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Pemulihan Akun</span>
            <h1 class="text-2xl font-extrabold text-neutral-950 tracking-tight">Lupa Kata Sandi?</h1>
            <p class="text-xs text-neutral-500 font-normal leading-relaxed">
                Masukkan alamat email yang terdaftar. Kami akan mengirimkan 6 digit kode OTP verifikasi untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        @if(session('status'))
            <div class="p-3.5 bg-neutral-900 text-white border border-neutral-800 rounded-xl text-xs font-medium flex items-start gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-green-400 text-sm mt-0.5 shrink-0"></i>
                <span class="leading-relaxed">{{ session('status') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium flex items-start gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-sm mt-0.5 shrink-0"></i>
                <span class="leading-relaxed">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="text-xs font-bold text-neutral-900">Alamat Email *</label>
                <div class="relative">
                    <input type="email" 
                           name="email" 
                           required 
                           autofocus
                           value="{{ old('email') }}" 
                           placeholder="nama@email.com"
                           class="w-full text-xs px-3.5 py-2.5 pl-9 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                    <i class="fa-regular fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                </div>
                @error('email')
                    <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-3.5 bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase tracking-widest font-bold rounded-xl active:scale-[0.98] transition shadow-xs hover:shadow-md cursor-pointer">
                Kirim Kode OTP Reset Sandi
            </button>
        </form>

        <p class="text-xs text-center text-neutral-500 pt-2 border-t border-neutral-100">
            Ingat kata sandi Anda? 
            <a href="{{ route('login') }}" class="font-bold text-neutral-950 hover:underline">Kembali ke halaman masuk</a>
        </p>
    </div>
</div>
@endsection

