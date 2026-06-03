@extends('layouts.app')

@section('page_title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-slate-800">Riwayat Transaksi</h1>
        <p class="text-sm text-slate-500 mt-0.5">Log lengkap semua aktivitas peminjaman dan pengembalian</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.history.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama mahasiswa atau barang..."
               class="flex-1 px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <select name="status" class="px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Semua Status</option>
            <option value="pending"          {{ request('status') === 'pending'          ? 'selected' : '' }}>Pending</option>
            <option value="approved"         {{ request('status') === 'approved'         ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected"         {{ request('status') === 'rejected'         ? 'selected' : '' }}>Ditolak</option>
            <option value="return_requested" {{ request('status') === 'return_requested' ? 'selected' : '' }}>Minta Kembali</option>
            <option value="returned"         {{ request('status') === 'returned'         ? 'selected' : '' }}>Selesai</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Filter
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.history.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mahasiswa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Barang</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($history as $record)
                    @php
                        $statusMap = [
                            'pending'          => ['bg-amber-100 text-amber-700',   'Pending'],
                            'approved'         => ['bg-blue-100 text-blue-700',     'Disetujui'],
                            'rejected'         => ['bg-red-100 text-red-700',       'Ditolak'],
                            'return_requested' => ['bg-purple-100 text-purple-700', 'Minta Kembali'],
                            'returned'         => ['bg-emerald-100 text-emerald-700','Selesai'],
                        ];
                        [$cls, $label] = $statusMap[$record->status] ?? ['bg-slate-100 text-slate-600', $record->status];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-400">{{ ($history->currentPage() - 1) * $history->perPage() + $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-medium text-slate-800">{{ $record->user->name ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-slate-800">{{ $record->item->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $record->item->category->name ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center font-mono-numbers text-sm text-slate-700">{{ $record->quantity }}</td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($record->borrow_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($record->return_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $cls }}">{{ $label }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Tidak ada data riwayat yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($history->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
