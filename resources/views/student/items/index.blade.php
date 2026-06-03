@extends('layouts.app')

@section('page_title', 'Cari Barang')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-slate-800">Cari Barang</h1>
        <p class="text-sm text-slate-500 mt-0.5">Temukan dan ajukan peminjaman barang laboratorium</p>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('student.items.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama barang..."
               class="flex-1 px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <select name="category" class="px-3.5 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Cari
        </button>
        @if(request('search') || request('category'))
            <a href="{{ route('student.items.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
        @endif
    </form>

    {{-- Items Grid --}}
    @if($items->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm text-slate-500">Tidak ada barang yang tersedia saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($items as $item)
                <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-blue-300 transition-all flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-100 text-emerald-700">Tersedia</span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-800 mb-1">{{ $item->name }}</h3>
                    <p class="text-xs text-slate-400 mb-3">{{ $item->category->name ?? '-' }}</p>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-lg font-bold text-blue-600 font-mono-numbers">{{ $item->stock }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide">Stok</p>
                        </div>
                        <div class="text-center">
                            @php $condLabel = ['good'=>'Baik','damaged'=>'Rusak','lost'=>'Hilang']; @endphp
                            <p class="text-sm font-semibold text-slate-700">{{ $condLabel[$item->condition] ?? $item->condition }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide">Kondisi</p>
                        </div>
                    </div>

                    {{-- Borrow Button triggers modal --}}
                    <div class="mt-auto" x-data="{ open: false }">
                        <button @click="open = true"
                                class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Ajukan Pinjam
                        </button>

                        {{-- Borrow Modal --}}
                        <div x-show="open" x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center p-4"
                             style="background: rgba(0,0,0,0.5);">
                            <div @click.outside="open = false"
                                 class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-base font-bold text-slate-800">Ajukan Peminjaman</h3>
                                    <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="bg-slate-50 rounded-lg p-3 mb-5">
                                    <p class="text-sm font-semibold text-slate-800">{{ $item->name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $item->category->name ?? '' }} &bull; Stok tersedia: <span class="font-semibold text-blue-600">{{ $item->stock }}</span></p>
                                </div>

                                <form action="{{ route('student.borrowings.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                            Jumlah <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $item->stock }}"
                                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                            Tanggal Pinjam <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="borrow_date" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}"
                                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                            Tanggal Rencana Kembali <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="return_date" value="{{ now()->addDays(7)->format('Y-m-d') }}" min="{{ now()->addDay()->format('Y-m-d') }}"
                                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div class="flex gap-3 pt-1">
                                        <button type="submit"
                                                class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            Kirim Permintaan
                                        </button>
                                        <button type="button" @click="open = false"
                                                class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $items->links() }}</div>
    @endif

</div>
@endsection
