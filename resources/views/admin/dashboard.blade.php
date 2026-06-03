@extends('layouts.app')

@section('page_title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Total Items --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Stok</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $totalItems }}</p>
            </div>
        </div>

        {{-- Available --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Tersedia</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $totalAvailable }}</p>
            </div>
        </div>

        {{-- Borrowed --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $totalBorrowed }}</p>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-rose-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $pendingCount + $returnPending }}</p>
                @if($returnPending > 0)
                    <p class="text-[10px] text-rose-500">{{ $returnPending }} pengembalian</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        {{-- Top Borrowed Items --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">🏆 Top Barang Paling Sering Dipinjam</h3>
            @if($topItems->isEmpty())
                <p class="text-sm text-slate-400 text-center py-6">Belum ada data peminjaman.</p>
            @else
                <div class="space-y-3">
                    @foreach($topItems as $index => $item)
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $item->name }}</p>
                                <p class="text-xs text-slate-400">{{ $item->category->name ?? '-' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-blue-600 font-mono-numbers shrink-0">{{ $item->borrow_count }}x</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">🕒 Aktivitas Terbaru</h3>
            @if($recentActivities->isEmpty())
                <p class="text-sm text-slate-400 text-center py-6">Belum ada aktivitas.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-xs font-bold text-slate-600 uppercase">
                                {{ substr($activity->user->name ?? '?', 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-slate-800">
                                    <span class="font-medium">{{ $activity->user->name ?? 'Unknown' }}</span>
                                    meminjam <span class="font-medium">{{ $activity->item->name ?? '-' }}</span>
                                    <span class="text-slate-500">({{ $activity->quantity }} unit)</span>
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                            @php
                                $statusMap = [
                                    'pending'          => ['bg-amber-100 text-amber-700', 'Pending'],
                                    'approved'         => ['bg-blue-100 text-blue-700', 'Disetujui'],
                                    'rejected'         => ['bg-red-100 text-red-700', 'Ditolak'],
                                    'return_requested' => ['bg-purple-100 text-purple-700', 'Dikembalikan'],
                                    'returned'         => ['bg-emerald-100 text-emerald-700', 'Selesai'],
                                ];
                                [$cls, $label] = $statusMap[$activity->status] ?? ['bg-slate-100 text-slate-600', $activity->status];
                            @endphp
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $cls }} shrink-0">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.categories.create') }}" class="bg-white border border-slate-200 rounded-xl p-4 hover:border-blue-400 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 bg-blue-50 group-hover:bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2 transition-colors">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-slate-700">Tambah Kategori</p>
        </a>
        <a href="{{ route('admin.items.create') }}" class="bg-white border border-slate-200 rounded-xl p-4 hover:border-blue-400 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2 transition-colors">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-slate-700">Tambah Barang</p>
        </a>
        <a href="{{ route('admin.borrowings.index') }}" class="bg-white border border-slate-200 rounded-xl p-4 hover:border-blue-400 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 bg-amber-50 group-hover:bg-amber-100 rounded-lg flex items-center justify-center mx-auto mb-2 transition-colors">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-slate-700">Persetujuan Pinjam</p>
            @if($pendingCount > 0)
                <span class="inline-block mt-1 bg-amber-500 text-white text-[10px] px-2 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.returns.index') }}" class="bg-white border border-slate-200 rounded-xl p-4 hover:border-blue-400 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 bg-purple-50 group-hover:bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2 transition-colors">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-slate-700">Konfirmasi Kembali</p>
            @if($returnPending > 0)
                <span class="inline-block mt-1 bg-purple-500 text-white text-[10px] px-2 rounded-full">{{ $returnPending }}</span>
            @endif
        </a>
    </div>

</div>
@endsection
