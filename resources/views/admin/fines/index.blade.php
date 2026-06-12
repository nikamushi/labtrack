@extends('layouts.app')

@section('page_title', 'Laporan Denda')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Laporan Denda Keterlambatan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Daftar mahasiswa yang dikenai denda karena pengembalian terlambat</p>
        </div>
        <a href="{{ route('admin.fines.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Akumulasi Denda</p>
                <p class="text-xl font-bold text-slate-800">Rp {{ number_format($totalFines, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Kasus Denda</p>
                <p class="text-xl font-bold text-slate-800">{{ $fines->total() }} Kasus</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.fines.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama mahasiswa atau barang..."
               class="flex-1 px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Filter
        </button>
        @if(request('search'))
            <a href="{{ route('admin.fines.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mahasiswa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Barang</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Dikembalikan</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterlambatan</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Denda</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($fines as $fine)
                    @php
                        $returnDate = \Carbon\Carbon::parse($fine->return_date);
                        $actualDate = \Carbon\Carbon::parse($fine->actual_return_date);
                        $daysOverdue = max(0, $returnDate->diffInDays($actualDate, false));
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-400 font-mono-numbers">{{ ($fines->currentPage() - 1) * $fines->perPage() + $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-medium text-slate-800">{{ $fine->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $fine->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-slate-800">{{ $fine->item->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $fine->item->category->name ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600">
                            {{ $fine->borrow_date ? $fine->borrow_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600">
                            {{ $returnDate->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600 font-medium">
                            {{ $actualDate->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm font-semibold text-red-600">
                            {{ $daysOverdue }} Hari
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-sm text-red-600">
                            Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-sm text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Tidak ada data denda yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($fines->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $fines->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
