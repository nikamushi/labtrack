@extends('layouts.app')

@section('page_title', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
        <p class="text-sm text-blue-200 mb-1">Selamat datang kembali 👋</p>
        <h1 class="text-2xl font-bold">{{ Auth::user()->name }}</h1>
        <p class="text-blue-200 text-sm mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $activeBorrowings }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $pendingRequests }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Riwayat Selesai</p>
                <p class="text-2xl font-bold text-slate-800 font-mono-numbers">{{ $historyCount }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        {{-- Recent Borrowings --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Peminjaman Terakhir</h3>
                <a href="{{ route('student.borrowings.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            @if($recentBorrowings->isEmpty())
                <p class="text-sm text-slate-400 text-center py-6">Belum ada peminjaman.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentBorrowings as $b)
                        @php
                            $statusMap = [
                                'pending'          => ['bg-amber-100 text-amber-700',   'Pending'],
                                'approved'         => ['bg-blue-100 text-blue-700',     'Disetujui'],
                                'rejected'         => ['bg-red-100 text-red-700',       'Ditolak'],
                                'return_requested' => ['bg-purple-100 text-purple-700', 'Minta Kembali'],
                                'returned'         => ['bg-emerald-100 text-emerald-700','Selesai'],
                            ];
                            [$cls, $label] = $statusMap[$b->status] ?? ['bg-slate-100 text-slate-600', $b->status];
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $b->item->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $b->item->category->name ?? '' }} &bull; {{ $b->quantity }} unit</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $cls }} shrink-0">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('student.items.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50 transition-all group">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Cari Barang</p>
                        <p class="text-xs text-slate-400">Temukan barang yang tersedia untuk dipinjam</p>
                    </div>
                </a>
                <a href="{{ route('student.borrowings.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50 transition-all group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Peminjaman Saya</p>
                        <p class="text-xs text-slate-400">Lihat status dan kelola peminjaman aktif</p>
                    </div>
                </a>
                <a href="{{ route('student.history.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50 transition-all group">
                    <div class="w-9 h-9 rounded-lg bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Riwayat Saya</p>
                        <p class="text-xs text-slate-400">Lihat semua riwayat peminjaman yang sudah selesai</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
