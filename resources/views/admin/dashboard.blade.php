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

    {{-- Advanced Analytics Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Trend Chart --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">📈 Tren Peminjaman</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah unit barang yang disetujui dipinjam dalam 7 hari terakhir</p>
            <div class="h-64 relative">
                <canvas id="borrowingTrendChart"></canvas>
            </div>
        </div>

        {{-- Category Doughnut Chart --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">📦 Distribusi Kategori</h3>
            <p class="text-xs text-slate-400 mb-4">Proporsi total stok barang berdasarkan kategori</p>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Second Row: Condition, Top Items, Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Condition Chart --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-1">🔧 Kondisi Fisik Barang</h3>
                <p class="text-xs text-slate-400 mb-4">Jumlah total unit barang berdasarkan kondisi fisik</p>
                <div class="h-56 relative flex items-center justify-center">
                    <canvas id="conditionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Borrowed Items --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-1 font-semibold">🏆 Barang Paling Populer</h3>
            <p class="text-xs text-slate-400 mb-4">Daftar barang yang paling sering dipinjam oleh mahasiswa</p>
            @if($topItems->isEmpty())
                <p class="text-sm text-slate-400 text-center py-12">Belum ada data peminjaman.</p>
            @else
                <div class="space-y-4">
                    @foreach($topItems as $index => $item)
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $item->name }}</p>
                                <p class="text-xs text-slate-400">{{ $item->category->name ?? '-' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-blue-600 font-mono-numbers shrink-0">{{ $item->borrow_count }}x pinjam</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">🕒 Aktivitas Terbaru</h3>
            <p class="text-xs text-slate-400 mb-4">Log peminjaman dan pengembalian terbaru</p>
            @if($recentActivities->isEmpty())
                <p class="text-sm text-slate-400 text-center py-12">Belum ada aktivitas.</p>
            @else
                <div class="space-y-4 max-h-[260px] overflow-y-auto pr-1">
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
                                    'return_requested' => ['bg-purple-100 text-purple-700', 'Kembali'],
                                    'returned'         => ['bg-emerald-100 text-emerald-700', 'Selesai'],
                                ];
                                [$cls, $label] = $statusMap[$activity->status] ?? ['bg-slate-100 text-slate-600', $activity->status];
                            @endphp
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-medium {{ $cls }} shrink-0">{{ $label }}</span>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Borrowing Trend Chart
        const trendCtx = document.getElementById('borrowingTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                    label: 'Unit Dipinjam',
                    data: @json($trendValues),
                    borderColor: 'rgb(37, 99, 235)',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(37, 99, 235)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // 2. Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    data: @json($categoryStocks),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });

        // 3. Condition Distribution Chart
        const conditionCtx = document.getElementById('conditionChart').getContext('2d');
        new Chart(conditionCtx, {
            type: 'bar',
            data: {
                labels: @json($conditionDataset['labels']),
                datasets: [{
                    data: @json($conditionDataset['values']),
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)', // Baik
                        'rgba(245, 158, 11, 0.8)', // Rusak
                        'rgba(239, 68, 68, 0.8)',  // Hilang
                        'rgba(59, 130, 246, 0.8)'  // Perawatan
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });
    });
</script>
@endsection
