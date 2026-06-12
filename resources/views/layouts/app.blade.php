<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LabTrack') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            /* Tabular figures for alignment of numerical data */
            .font-mono-numbers {
                font-variant-numeric: tabular-nums;
            }
        </style>
    </head>
    <body class="bg-[#f8f9ff] text-[#0b1c30] antialiased">
        <div class="flex min-h-screen">
            <!-- Sidebar Layout (Fixed 260px width) -->
            <aside class="w-[260px] h-screen sticky top-0 bg-[#1e293b] text-white flex flex-col shrink-0 border-r border-[#334155]">
                <!-- Sidebar Header -->
                <div class="h-16 flex items-center px-6 border-b border-[#334155]">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="text-lg font-bold tracking-tight">LabTrack</span>
                    </a>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                    @if (Auth::user()->role === 'admin')
                        <!-- Admin Navigation links -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Kategori
                        </a>
                        <a href="{{ route('admin.items.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.items.*') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Barang
                        </a>
                        <a href="{{ route('admin.students.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Mahasiswa
                        </a>
                        <a href="{{ route('admin.borrowings.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.borrowings.index') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Peminjaman
                        </a>
                        <a href="{{ route('admin.returns.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.returns.index') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pengembalian
                        </a>
                        <a href="{{ route('admin.history.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.history.index') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Riwayat
                        </a>
                        <a href="{{ route('admin.fines.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('admin.fines.*') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Laporan Denda
                        </a>
                    @else
                        <!-- Student Navigation links -->
                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('student.dashboard') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('student.items.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('student.items.index') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Cari Barang
                        </a>
                        <a href="{{ route('student.borrowings.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('student.borrowings.index') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Peminjaman Saya
                        </a>
                        <a href="{{ route('student.history.index') }}" 
                           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('student.history.index') ? 'bg-[#2563eb] text-white font-medium' : 'text-slate-300 hover:bg-[#334155] hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Riwayat Saya
                        </a>
                    @endif
                </nav>

                <!-- Logout / Profile Section at Bottom of Sidebar -->
                <div class="p-4 border-t border-[#334155] bg-[#0f172a]/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-[#2563eb] flex items-center justify-center font-bold text-xs uppercase text-white">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate capitalize">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('profile.edit') }}" class="flex-1 text-center py-1.5 text-xs bg-[#334155] hover:bg-slate-600 rounded text-slate-200 transition-colors">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-center py-1.5 text-xs bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded transition-all">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Right Container -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Navbar (Fixed 64px height) -->
                <header class="h-16 bg-white border-b border-[#e2e8f0] flex items-center justify-between px-8 shrink-0">
                    <!-- Page Context Info -->
                    <div class="flex items-center">
                        <h2 class="text-base font-semibold text-[#0b1c30]">
                            @yield('page_title', 'Sistem Inventaris')
                        </h2>
                    </div>

                    <!-- Breadcrumbs or Status Info -->
                    <div class="text-xs text-slate-500">
                        {{ now()->format('l, d F Y') }}
                    </div>
                </header>

                <!-- Scrollable Main Content Container -->
                <main class="flex-1 overflow-auto p-8 max-w-[1440px] w-full mx-auto">
                    <!-- Dynamic Alert Flash Messages -->
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded text-emerald-800 text-sm flex items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded text-red-800 text-sm flex items-center">
                            <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded text-amber-800 text-sm flex items-center">
                            <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            {{ session('warning') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
