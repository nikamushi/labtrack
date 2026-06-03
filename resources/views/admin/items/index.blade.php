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
                                    'good'    => 'bg-emerald-100 text-emerald-700',
                                    'damaged' => 'bg-amber-100 text-amber-700',
                                    'lost'    => 'bg-red-100 text-red-700',
                                ];
                                $conditionLabel = ['good' => 'Baik', 'damaged' => 'Rusak', 'lost' => 'Hilang'];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $conditionMap[$item->condition] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $conditionLabel[$item->condition] ?? $item->condition }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($item->status === 'available')
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-blue-50 text-blue-700">Tersedia</span>
                            @else
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-500">Tidak Tersedia</span>
                            @endif
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
