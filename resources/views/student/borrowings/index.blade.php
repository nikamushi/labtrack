@extends('layouts.app')

@section('page_title', 'Peminjaman Saya')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-slate-800">Peminjaman Saya</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola semua peminjaman dan ajukan pengembalian</p>
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
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($borrowings as $borrowing)
                    @php
                        $statusMap = [
                            'pending'          => ['bg-amber-100 text-amber-700',    'Menunggu'],
                            'approved'         => ['bg-blue-100 text-blue-700',      'Disetujui'],
                            'rejected'         => ['bg-red-100 text-red-700',        'Ditolak'],
                            'return_requested' => ['bg-purple-100 text-purple-700',  'Minta Kembali'],
                            'returned'         => ['bg-emerald-100 text-emerald-700','Selesai'],
                        ];
                        [$cls, $label] = $statusMap[$borrowing->status] ?? ['bg-slate-100 text-slate-600', $borrowing->status];
                        $dueDate = \Carbon\Carbon::parse($borrowing->return_date);
                        $isOverdue = $borrowing->status === 'approved' && $dueDate->isPast();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $isOverdue ? 'bg-red-50' : '' }}">
                        <td class="px-5 py-3.5 text-sm text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-slate-800">{{ $borrowing->item->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $borrowing->item->category->name ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center font-mono-numbers text-sm font-semibold text-slate-700">
                            {{ $borrowing->quantity }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm">
                            <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                {{ $dueDate->format('d M Y') }}
                            </span>
                            @if($isOverdue)
                                <span class="block text-[10px] text-red-500 font-medium">⚠ Terlambat</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($borrowing->status === 'approved')
                                <form action="{{ route('student.returns.store', $borrowing) }}" method="POST"
                                      onsubmit="return confirm('Konfirmasi pengembalian barang ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        Kembalikan
                                    </button>
                                </form>
                            @elseif($borrowing->status === 'return_requested')
                                <span class="text-xs text-purple-500 italic">Menunggu konfirmasi admin</span>
                            @elseif($borrowing->status === 'pending')
                                <span class="text-xs text-slate-400 italic">Menunggu persetujuan</span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Belum ada peminjaman.
                            <a href="{{ route('student.items.index') }}" class="text-blue-600 hover:underline ml-1">Cari barang sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($borrowings->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
