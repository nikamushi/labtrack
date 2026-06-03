@extends('layouts.app')

@section('page_title', 'Manajemen Barang')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Inventaris Barang</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola seluruh barang di laboratorium</p>
        </div>
        <a href="{{ route('admin.items.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Barang
        </a>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalItems     = $items->total();
        $availableCount = $allItems->where('status','available')->count();
        $borrowedCount  = $allItems->where('status','borrowed')->count();
        $maintenCount   = $allItems->whereIn('status',['maintenance','unavailable'])->count();
    @endphp
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Total Barang</p>
                <p class="text-lg font-bold text-slate-800">{{ $totalItems }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Tersedia</p>
                <p class="text-lg font-bold text-emerald-600">{{ $availableCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Dipinjam</p>
                <p class="text-lg font-bold text-orange-600">{{ $borrowedCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Perawatan / Hilang</p>
                <p class="text-lg font-bold text-red-500">{{ $maintenCount }}</p>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
        <form method="GET" action="{{ route('admin.items.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-slate-600 mb-1">Cari Barang</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama barang..."
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-44">
                <label class="block text-xs font-medium text-slate-600 mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="available"   {{ request('status') === 'available'   ? 'selected' : '' }}>Tersedia</option>
                    <option value="borrowed"    {{ request('status') === 'borrowed'    ? 'selected' : '' }}>Dipinjam</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                    <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
                @if(request()->hasAny(['search','category','status']))
                    <a href="{{ route('admin.items.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-8">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-400 font-mono-numbers">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-semibold text-slate-800">{{ $item->name }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-500">{{ $item->category->name ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-center font-mono-numbers text-sm font-semibold text-slate-700">{{ $item->stock }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @php
                                $conditionMap = [
                                    'good'        => 'bg-emerald-100 text-emerald-700',
                                    'damaged'     => 'bg-amber-100 text-amber-700',
                                    'lost'        => 'bg-red-100 text-red-700',
                                    'maintenance' => 'bg-blue-100 text-blue-700',
                                ];
                                $conditionLabel = [
                                    'good'        => 'Baik',
                                    'damaged'     => 'Rusak',
                                    'lost'        => 'Hilang',
                                    'maintenance' => 'Perawatan',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $conditionMap[$item->condition] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $conditionLabel[$item->condition] ?? $item->condition }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @php
                                $statusMap = [
                                    'available'   => 'bg-emerald-100 text-emerald-700',
                                    'borrowed'    => 'bg-orange-100 text-orange-700',
                                    'maintenance' => 'bg-blue-100 text-blue-700',
                                    'unavailable' => 'bg-red-100 text-red-700',
                                ];
                                $statusLabel = [
                                    'available'   => 'Tersedia',
                                    'borrowed'    => 'Dipinjam',
                                    'maintenance' => 'Perawatan',
                                    'unavailable' => 'Tidak Tersedia',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $statusMap[$item->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $statusLabel[$item->status] ?? $item->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.items.edit', $item) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus barang \'{{ $item->name }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Belum ada barang. <a href="{{ route('admin.items.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($items->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
