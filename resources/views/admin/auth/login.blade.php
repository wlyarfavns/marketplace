<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Administrator — FOOTWORK Back-Office</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white border border-gray-200 p-8 sm:p-10 space-y-6">
        <div class="text-center space-y-2">
            <span class="text-2xl font-serif font-bold text-gray-950 uppercase tracking-tight block">
                FOOTWORK
            </span>
            <span class="text-[10px] uppercase tracking-widest text-gray-400 font-bold block">Back-Office Administrator</span>
            <p class="text-xs text-gray-500 font-light">Masuk untuk mengelola seluruh 16 modul operasional marketplace.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-800 text-xs p-3 border-l-4 border-red-500">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700">Email Administrator</label>
                <div class="relative">
                    <input type="email" name="email" required value="{{ old('email', 'admin@footwork.id') }}" placeholder="admin@footwork.id"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                </div>
                @error('email')
                    <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700">Kata Sandi</label>
                <input type="password" name="password" required value="password" placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                    <input type="checkbox" name="remember" checked class="text-black focus:ring-0">
                    <span>Ingat Sesi Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-black text-white text-xs uppercase tracking-widest font-semibold hover:bg-black transition">
                Masuk ke Panel Admin &rarr;
            </button>
        </form>

        <div class="p-3 bg-gray-50 border border-gray-200 text-xs text-gray-500 text-center">
            <span class="font-semibold text-gray-800">Akun Super Admin:</span> admin@footwork.id &bull; Pass: <code class="font-mono text-black">password</code>
        </div>

        <div class="text-center pt-2 border-t border-gray-100">
            <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-black transition">
                &larr; Kembali ke Website Toko
            </a>
        </div>
    </div>

</body>
</html>

