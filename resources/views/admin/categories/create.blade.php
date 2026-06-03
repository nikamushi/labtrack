@extends('layouts.app')

@section('page_title', 'Tambah Kategori')

@section('content')
<div class="max-w-lg">
    <div class="mb-5">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Kategori
        </a>
        <h1 class="text-xl font-bold text-slate-800 mt-2">Tambah Kategori Baru</h1>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-400 @enderror"
                       placeholder="Contoh: Elektronik, Peralatan Optik">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                          placeholder="Deskripsi singkat kategori (opsional)">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
