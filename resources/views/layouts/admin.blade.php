<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950" x-data="{ sidebarOpen: false }" x-init="sidebarOpen = window.innerWidth >= 1024" @resize.window="sidebarOpen = window.innerWidth >= 1024">
    <div class="flex h-full">
        <!-- Mobile overlay backdrop -->
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-xl lg:shadow-none transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:z-auto lg:pointer-events-auto pointer-events-none"
               :class="{ 'translate-x-0 pointer-events-auto': sidebarOpen }"
               @click.outside="if(window.innerWidth < 1024) sidebarOpen = false">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                    🎓 Cadet Academy
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" @click="if(window.innerWidth < 1024) sidebarOpen = false">
                <!-- 🏠 Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg mb-1 {{ request()->routeIs('admin.dashboard') && !request()->segment(2) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">🏠</span> Dashboard
                </a>

                <!-- 👨‍🎓 Akademik -->
                <div x-data="{ open: {{ request()->is('admin/akademik*') || request()->is('admin/users*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">👨‍🎓</span> Akademik</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Siswa</a>
                        <a href="{{ route('admin.akademik.orang-tua.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.akademik.orang-tua.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Orang Tua</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Tutor</a>
                        <a href="{{ route('admin.akademik.kelas.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.akademik.kelas.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Kelas</a>
                        <a href="{{ route('admin.akademik.jadwal.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.akademik.jadwal.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Jadwal</a>
                        <a href="{{ route('admin.akademik.materi.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.akademik.materi.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Materi</a>
                        <a href="{{ route('admin.akademik.tugas.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.akademik.tugas.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Tugas</a>
                        <a href="{{ route('admin.akademik.sertifikat.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.akademik.sertifikat.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Sertifikat</a>
                    </div>
                </div>

                <!-- 📝 CBT -->
                <div x-data="{ open: {{ request()->is('admin/exams*') || request()->is('admin/cbt*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📝</span> CBT</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Bank Soal</a>
                        <a href="{{ route('admin.exams.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.exams.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Try Out</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Hasil</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Analitik</a>
                    </div>
                </div>

                <!-- 📍 Absensi -->
                <div x-data="{ open: {{ request()->is('attendance*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📍</span> Absensi</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('admin.attendance') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.attendance') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Kehadiran</a>
                        <a href="{{ route('admin.lokasi.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.lokasi.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Lokasi</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Laporan</a>
                    </div>
                </div>

                <!-- 💳 Keuangan -->
                <div x-data="{ open: {{ request()->is('admin/keuangan*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">💳</span> Keuangan</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.keuangan.tagihan.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Tagihan</a>
                        <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.keuangan.pembayaran.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Pembayaran</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Invoice</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Laporan</a>
                    </div>
                </div>

                <!-- 📢 CRM & Marketing -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📢</span> CRM & Marketing</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('admin.crm.leads.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.crm.leads.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Leads</a>
                        <a href="{{ route('admin.crm.kampanye.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.crm.kampanye.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Kampanye</a>
                        <a href="{{ route('admin.crm.broadcast.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.crm.broadcast.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Broadcast</a>
                        <a href="{{ route('admin.crm.testimoni.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.crm.testimoni.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Testimoni</a>
                    </div>
                </div>

                <!-- 🌐 Website -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">🌐</span> Website</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Landing Page</a>
                        <a href="{{ route('admin.website.blog.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.website.blog.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Blog</a>
                        <a href="{{ route('admin.website.faq.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.website.faq.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">FAQ</a>
                        <a href="{{ route('admin.website.banner.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.website.banner.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Banner</a>
                    </div>
                </div>

                <!-- ⚙ Pengaturan -->
                <div x-data="{ open: {{ request()->is('admin/settings*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                        <span class="flex items-center"><span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">⚙</span> Pengaturan</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">User & Role</a>
                        <a href="{{ route('admin.settings.smtp.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.settings.smtp.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">SMTP</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">WhatsApp</a>
                        <a href="{{ route('admin.settings.payment-gateway.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.settings.payment-gateway.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Payment Gateway</a>
                        <a href="#" class="block px-3 py-1.5 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">PWA</a>
                        <a href="{{ route('admin.settings.audit-log.index') }}" class="block px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.settings.audit-log.*') ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">Audit Log</a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="sticky top-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="relative z-50 p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="flex items-center space-x-4 ml-auto">
                        <!-- Notifications -->
                        <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span id="unread-badge" class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full" style="display: none;">0</span>
                        </a>

                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm">
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                                <span class="hidden sm:block font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                            </button>
                            <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        // Poll unread notifications count
        setInterval(async () => {
            try {
                const res = await fetch('{{ route("notifications.unread-count") }}');
                const data = await res.json();
                const badge = document.getElementById('unread-badge');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-flex';
                } else {
                    badge.style.display = 'none';
                }
            } catch(e) {}
        }, 30000);
    </script>
</body>
</html>
