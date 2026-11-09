@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border border-neutral-200/90 rounded-3xl p-8 sm:p-10 bg-white space-y-6 shadow-xs">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-neutral-100 flex items-center justify-center text-neutral-900">
                <i class="fa-solid fa-lock text-lg"></i>
            </div>
            <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Keamanan Akun</span>
            <h1 class="text-2xl font-extrabold text-neutral-950 tracking-tight">Atur Ulang Kata Sandi</h1>
            <p class="text-xs text-neutral-500 font-normal leading-relaxed">
                Silakan buat kata sandi baru yang kuat untuk melindungi akun FOOTWORK Anda.
            </p>
        </div>

        @if($errors->any())
            <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500 text-xs mt-0.5 shrink-0"></i>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-1">
                <label class="text-xs font-bold text-neutral-900">Alamat Email *</label>
                <div class="relative">
                    <input type="email" 
                           name="email" 
                           required 
                           value="{{ old('email', $email) }}" 
                           placeholder="nama@email.com"
                           class="w-full text-xs px-3.5 py-2.5 pl-9 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-neutral-50/50">
                    <i class="fa-regular fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                </div>
            </div>

            <div class="space-y-1" x-data="{ showPassword: false }">
                <label class="text-xs font-bold text-neutral-900">Kata Sandi Baru (Min 6 Karakter) *</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autofocus
                           placeholder="••••••••"
                           class="w-full text-xs px-3.5 py-2.5 pr-10 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-700 transition cursor-pointer p-1"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-1" x-data="{ showConfirm: false }">
                <label class="text-xs font-bold text-neutral-900">Konfirmasi Kata Sandi Baru *</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" 
                           name="password_confirmation" 
                           required 
                           placeholder="••••••••"
                           class="w-full text-xs px-3.5 py-2.5 pr-10 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                    <button type="button" 
                            @click="showConfirm = !showConfirm" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-700 transition cursor-pointer p-1"
                            title="Tampilkan / Sembunyikan Konfirmasi Sandi">
                        <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-sm"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase tracking-widest font-bold rounded-xl active:scale-[0.98] transition shadow-xs hover:shadow-md cursor-pointer">
                Simpan Kata Sandi Baru
            </button>
        </form>

        <p class="text-xs text-center text-neutral-500 pt-2 border-t border-neutral-100">
            Ingat kata sandi Anda? 
            <a href="{{ route('login') }}" class="font-bold text-neutral-950 hover:underline">Kembali ke halaman masuk</a>
        </p>
    </div>
</div>
@endsection

