@extends('layouts.app')

@section('page_title', 'Tambah Barang')

@section('content')
<div class="max-w-lg">
    <div class="mb-5">
        <a href="{{ route('admin.items.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Barang
        </a>
        <h1 class="text-xl font-bold text-slate-800 mt-2">Tambah Barang Baru</h1>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('admin.items.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nama Barang <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="Contoh: Mikroskop Binokuler"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select id="category_id" name="category_id"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-400 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Stok <span class="text-red-500">*</span>
                </label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', 1) }}" min="0"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-400 @enderror">
                @error('stock')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="condition" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Kondisi <span class="text-red-500">*</span>
                </label>
                <select id="condition" name="condition"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('condition') border-red-400 @enderror">
                    <option value="good"    {{ old('condition') === 'good'    ? 'selected' : '' }}>Baik</option>
                    <option value="damaged" {{ old('condition') === 'damaged' ? 'selected' : '' }}>Rusak</option>
                    <option value="lost"    {{ old('condition') === 'lost'    ? 'selected' : '' }}>Hilang</option>
                </select>
                @error('condition')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Simpan Barang
                </button>
                <a href="{{ route('admin.items.index') }}"
                   class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
