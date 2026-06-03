@extends('layouts.app')

@section('page_title', 'Konfirmasi Pengembalian')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-slate-800">Konfirmasi Pengembalian</h1>
        <p class="text-sm text-slate-500 mt-0.5">Tinjau dan konfirmasi pengembalian barang dari mahasiswa</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mahasiswa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Barang</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Rencana Kembali</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($borrowings as $borrowing)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-medium text-slate-800">{{ $borrowing->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $borrowing->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-slate-800">{{ $borrowing->item->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $borrowing->item->category->name ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center font-mono-numbers text-sm font-semibold text-slate-700">{{ $borrowing->quantity }}</td>
                        <td class="px-5 py-3.5 text-center text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-sm">
                            @php $dueDate = \Carbon\Carbon::parse($borrowing->return_date); @endphp
                            <span class="{{ $dueDate->isPast() ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                {{ $dueDate->format('d M Y') }}
                                @if($dueDate->isPast())
                                    <span class="block text-xs text-red-500">(Terlambat)</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <form action="{{ route('admin.returns.approve', $borrowing) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Konfirmasi Kembali
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tidak ada permintaan pengembalian saat ini.
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
