@extends('layouts.app')

@section('page_title', 'Pengaturan Profil')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Pengaturan Profil</h1>
        <p class="text-sm text-slate-500 mt-0.5">Perbarui informasi profil akun dan kata sandi Anda</p>
    </div>

    {{-- Session Status Success --}}
    @if (session('status') === 'profile-updated')
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm flex items-center">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informasi profil berhasil diperbarui.
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm flex items-center">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Kata sandi berhasil diperbarui.
        </div>
    @endif

    {{-- Update Profile Info Form --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="max-w-xl">
            <section>
                <header class="mb-4">
                    <h2 class="text-base font-bold text-slate-800">
                        Informasi Profil
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Perbarui nama akun dan alamat email Anda.
                    </p>
                </header>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    {{-- Update Password Form --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="max-w-xl">
            <section>
                <header class="mb-4">
                    <h2 class="text-base font-bold text-slate-800">
                        Perbarui Kata Sandi
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pastikan akun Anda menggunakan kata sandi acak yang panjang untuk menjaga keamanan.
                    </p>
                </header>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-slate-700 mb-1.5">Kata Sandi Saat Ini</label>
                        <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password', 'updatePassword') border-red-400 @enderror">
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-slate-700 mb-1.5">Kata Sandi Baru</label>
                        <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password', 'updatePassword') border-red-400 @enderror">
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password_confirmation', 'updatePassword') border-red-400 @enderror">
                        @error('password_confirmation', 'updatePassword')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Perbarui Sandi
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
