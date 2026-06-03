@extends('layouts.app')

@section('page_title', 'Riwayat Saya')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-slate-800">Riwayat Peminjaman Saya</h1>
        <p class="text-sm text-slate-500 mt-0.5">Semua riwayat peminjaman yang telah selesai atau ditolak</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
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
                            'returned' => ['bg-emerald-100 text-emerald-700', 'Selesai'],
                            'rejected' => ['bg-red-100 text-red-700',         'Ditolak'],
                        ];
                        [$cls, $label] = $statusMap[$record->status] ?? ['bg-slate-100 text-slate-600', $record->status];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-400">{{ ($history->currentPage() - 1) * $history->perPage() + $loop->iteration }}</td>
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
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Belum ada riwayat peminjaman.
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
