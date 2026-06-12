<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - LabTrack</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] antialiased">

<div class="min-h-screen flex flex-col md:flex-row">
    
    <!-- Left Panel: Brand & Aesthetics (Visible on md and up) -->
    <div class="hidden md:flex md:w-1/2 bg-[#0f172a] relative overflow-hidden flex-col justify-between p-12 text-white">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 via-transparent to-purple-600/20 z-0"></div>
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl z-0"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-purple-500/10 blur-3xl z-0"></div>

        <!-- Brand Logo -->
        <div class="flex items-center space-x-3 z-10">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight">LabTrack</span>
        </div>

        <!-- Central Slogan/Visual Text -->
        <div class="my-auto z-10 max-w-md space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight leading-none text-white">
                Sistem Peminjaman Inventaris Laboratorium
            </h1>
            <p class="text-slate-400 text-sm leading-relaxed">
                Kelola inventaris laboratorium, lacak peminjaman barang, serta pantau status ketersediaan secara real-time dan transparan dalam satu platform terpadu.
            </p>
        </div>

        <!-- Footer Info -->
        <div class="text-xs text-slate-500 z-10">
            &copy; {{ date('Y') }} LabTrack. Rekayasa Perangkat Lunak Proyek UAS.
        </div>
    </div>

    <!-- Right Panel: Login Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 md:w-1/2 bg-white">
        <div class="w-full max-w-md space-y-8">
            
            <!-- Mobile Header Logo (Visible on mobile only) -->
            <div class="md:hidden flex flex-col items-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-slate-900 tracking-tight">LabTrack</span>
            </div>

            <!-- Page Title/Instruction -->
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
                <p class="text-sm text-slate-500 mt-1.5">Silakan masuk menggunakan kredensial akun Anda.</p>
            </div>

            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Main Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="nama@email.com"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-400 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Kata Sandi
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-blue-600 hover:text-blue-700 transition-colors" href="{{ route('password.request') }}">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-400 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me checkbox -->
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <label for="remember_me" class="ms-2 text-sm text-slate-600">Ingat perangkat ini</label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-lg shadow-sm shadow-blue-500/20 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Masuk ke Aplikasi
                    </button>
                </div>
            </form>
    </div>
</div>

</body>
</html>
