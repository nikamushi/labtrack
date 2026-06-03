@extends('layouts.app')

@section('page_title', 'Edit Mahasiswa')

@section('content')
<div class="max-w-lg">
    <div class="mb-5">
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Mahasiswa
        </a>
        <h1 class="text-xl font-bold text-slate-800 mt-2">Edit Data Mahasiswa</h1>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-400 @enderror"
                       placeholder="Masukkan nama lengkap mahasiswa">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-400 @enderror"
                       placeholder="Contoh: mahasiswa@univ.ac.id">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="text-sm font-semibold text-slate-800 mb-2">Ubah Kata Sandi (Opsional)</h3>
                <p class="text-xs text-slate-500 mb-3">Biarkan kosong jika tidak ingin mengubah kata sandi.</p>

                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <input type="password" id="password" name="password"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-400 @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ulangi kata sandi baru">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.students.index') }}"
                   class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
