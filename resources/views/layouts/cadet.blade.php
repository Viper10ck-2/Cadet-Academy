<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><meta name="theme-color" content="#0F172A">
    <title>@yield('title','Cadet') | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}
    .nav-item-cdt{display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;border-radius:.75rem;font-size:.875rem;font-weight:500;color:rgba(255,255,255,.55);transition:all .2s}
    .nav-item-cdt:hover{background:rgba(255,255,255,.08);color:#fff}
    .nav-cdt-active{background:rgba(255,255,255,.12);color:#fff;font-weight:600}
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-navy-900 dark:text-gray-100 font-sans antialiased"
      x-data="{ theme: localStorage.getItem('theme') || 'light', sidebarOpen: window.innerWidth >= 1024 }"
      x-init="$watch('theme', val => { document.documentElement.classList.toggle('dark', val === 'dark'); localStorage.setItem('theme', val); })"
      :class="theme === 'dark' ? 'dark' : ''"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">
    <div class="flex min-h-screen overflow-hidden">
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/30 backdrop-blur-sm lg:hidden" x-transition></div>
        <aside class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-navy-900 text-white shadow-xl shadow-black/10 transform transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto lg:shadow-none"
               :class="{ 'translate-x-0': sidebarOpen }">
            <div class="flex items-center h-16 px-5 shrink-0">
                <a href="{{ route('cadet.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-accent-500 flex items-center justify-center text-white font-extrabold text-sm shadow-lg shadow-accent-500/30">CA</div>
                    <span class="font-display font-bold text-base tracking-tight">Cadet Academy</span>
                </a>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
                <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-500">Menu</p>
                <a href="{{ route('cadet.dashboard') }}" class="nav-item-cdt {{ request()->routeIs('cadet.dashboard') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('cadet.classes') }}" class="nav-item-cdt {{ request()->routeIs('cadet.classes') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Kelas</span>
                </a>
                <a href="{{ route('cadet.schedule') }}" class="nav-item-cdt {{ request()->routeIs('cadet.schedule') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Jadwal</span>
                </a>
                <a href="{{ route('cadet.materials') }}" class="nav-item-cdt {{ request()->routeIs('cadet.materials') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Materi</span>
                </a>
                <a href="{{ route('cadet.assignments') }}" class="nav-item-cdt {{ request()->routeIs('cadet.assignments*') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Tugas</span>
                </a>
                <a href="{{ route('cadet.cbt') }}" class="nav-item-cdt {{ request()->routeIs('cadet.cbt') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Try Out</span>
                </a>
                <a href="{{ route('cadet.attendance') }}" class="nav-item-cdt {{ request()->routeIs('cadet.attendance') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Absensi</span>
                </a>
                <a href="{{ route('cadet.grades') }}" class="nav-item-cdt {{ request()->routeIs('cadet.grades') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Nilai</span>
                </a>
                <a href="{{ route('cadet.achievements') }}" class="nav-item-cdt {{ request()->routeIs('cadet.achievements') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span>Prestasi</span>
                </a>
                <a href="{{ route('cadet.discussions') }}" class="nav-item-cdt {{ request()->routeIs('cadet.discussions') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Diskusi</span>
                </a>
                <a href="{{ route('cadet.notifications') }}" class="nav-item-cdt {{ request()->routeIs('cadet.notifications') ? 'nav-cdt-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>Notifikasi</span>
                </a>
            </nav>
            <div class="p-3 border-t border-white/10 shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/8 transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0 lg:ml-64">
            <header class="sticky top-0 z-20 bg-white/70 dark:bg-navy-900/70 backdrop-blur-xl border-b border-gray-100 dark:border-navy-800">
                <div class="flex items-center justify-between h-14 px-4 lg:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="flex-1"></div>
                    <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-navy-800 transition-all">
                        <svg x-show="theme !== 'dark'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="theme === 'dark'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 animate-fade-in">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
