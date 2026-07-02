<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Instructor') | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950" x-data="{ sidebarOpen: false }" x-init="sidebarOpen = window.innerWidth >= 1024" @resize.window="sidebarOpen = window.innerWidth >= 1024">
    <div class="flex h-full">
        <!-- Overlay -->
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-emerald-900 to-emerald-950 border-r border-emerald-800 shadow-xl lg:shadow-none transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:z-auto lg:pointer-events-auto pointer-events-none"
               :class="{ 'translate-x-0 pointer-events-auto': sidebarOpen }"
               @click.outside="if(window.innerWidth < 1024) sidebarOpen = false">
            <div class="flex items-center justify-between h-16 px-6 border-b border-emerald-800">
                <a href="{{ route('instructor.dashboard') }}" class="text-xl font-bold text-emerald-400">
                    🎓 Cadet Academy
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md text-emerald-400 hover:text-emerald-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" @click="if(window.innerWidth < 1024) sidebarOpen = false">
                <p class="px-3 text-xs font-semibold text-emerald-400/60 uppercase tracking-wider mb-3">Panel Instruktur</p>

                {{-- 🏠 Dashboard --}}
                <a href="{{ route('instructor.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.dashboard') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">🏠</span> Dashboard
                </a>

                {{-- 📅 Jadwal Mengajar --}}
                <a href="{{ route('instructor.schedule') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.schedule') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📅</span> Jadwal Mengajar
                </a>

                {{-- 👨‍🏫 Kelas Saya --}}
                <a href="{{ route('instructor.classes') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.classes*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">👨‍🏫</span> Kelas Saya
                </a>

                {{-- 📚 Materi --}}
                <a href="{{ route('instructor.materials') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.materials*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📚</span> Materi
                </a>

                {{-- 📝 Tugas & Penilaian --}}
                <a href="{{ route('instructor.assignments') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.assignments*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📝</span> Tugas & Penilaian
                </a>

                {{-- 💻 CBT / Quiz --}}
                <a href="{{ route('instructor.cbt') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.cbt*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">💻</span> CBT / Quiz
                </a>

                {{-- 📍 Absensi --}}
                <a href="{{ route('instructor.attendance') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.attendance*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📍</span> Absensi
                </a>

                {{-- 💬 Pengumuman & Pesan --}}
                <a href="{{ route('instructor.announcements') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.announcements*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">💬</span> Pengumuman & Pesan
                </a>

                {{-- 📊 Laporan --}}
                <a href="{{ route('instructor.reports') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('instructor.reports*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">📊</span> Laporan
                </a>

                {{-- 👤 Profil --}}
                <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('profile.*') ? 'bg-emerald-800 text-emerald-200' : 'text-emerald-200/80 hover:bg-emerald-800/50 hover:text-emerald-100' }}">
                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-lg">👤</span> Profil
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="relative z-50 p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="flex items-center space-x-4 ml-auto">
                        <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm">
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                                <span class="hidden sm:block font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                            </button>
                            <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border z-50 py-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg text-sm">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg text-sm">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
