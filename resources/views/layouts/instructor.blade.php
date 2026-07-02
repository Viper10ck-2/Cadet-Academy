<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><meta name="theme-color" content="#0F172A">
    <title>@yield('title','Instructor') | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}
    .nav-item-inst{display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;border-radius:.75rem;font-size:.875rem;font-weight:500;color:rgba(255,255,255,.6);transition:all .2s}
    .nav-item-inst:hover{background:rgba(255,255,255,.08);color:#fff}
    .nav-inst-active{background:rgba(255,255,255,.12);color:#fff;font-weight:600}
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-900 dark:bg-navy dark:text-gray-100 font-sans antialiased"
      x-data="{ theme: localStorage.getItem('theme') || 'light', sidebarOpen: window.innerWidth >= 1024 }"
      x-init="$watch('theme', val => { document.documentElement.classList.toggle('dark', val === 'dark'); localStorage.setItem('theme', val); })"
      :class="theme === 'dark' ? 'dark' : ''"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">
    <div class="flex h-full overflow-hidden">
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden" x-transition></div>
        <aside class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-gradient-to-b from-emerald-900 to-emerald-950 text-white shadow-2xl transform transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto"
               :class="{ 'translate-x-0': sidebarOpen }">
            <div class="flex items-center h-16 px-5 border-b border-emerald-800/50 shrink-0">
                <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white font-extrabold text-sm">CA</div>
                    <span class="font-display font-bold text-base">Cadet Academy</span>
                </a>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <p class="px-3 py-1 text-[10px] font-semibold uppercase tracking-widest text-emerald-300/60">Instructor Panel</p>
                <a href="{{ route('instructor.dashboard') }}" class="nav-item-inst {{ request()->routeIs('instructor.dashboard') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('instructor.schedule') }}" class="nav-item-inst {{ request()->routeIs('instructor.schedule') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Jadwal</span>
                </a>
                <a href="{{ route('instructor.classes') }}" class="nav-item-inst {{ request()->routeIs('instructor.classes*') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Kelas</span>
                </a>
                <a href="{{ route('instructor.materials') }}" class="nav-item-inst {{ request()->routeIs('instructor.materials') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Materi</span>
                </a>
                <a href="{{ route('instructor.assignments') }}" class="nav-item-inst {{ request()->routeIs('instructor.assignments*') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Tugas</span>
                </a>
                <a href="{{ route('instructor.attendance') }}" class="nav-item-inst {{ request()->routeIs('instructor.attendance*') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Absensi</span>
                </a>
                <a href="{{ route('instructor.cbt') }}" class="nav-item-inst {{ request()->routeIs('instructor.cbt*') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>CBT</span>
                </a>
                <a href="{{ route('instructor.announcements') }}" class="nav-item-inst {{ request()->routeIs('instructor.announcements*') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span>Pengumuman</span>
                </a>
                <a href="{{ route('instructor.reports') }}" class="nav-item-inst {{ request()->routeIs('instructor.reports*') ? 'nav-inst-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan</span>
                </a>
            </nav>
            <div class="p-3 border-t border-emerald-800/50 shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-emerald-200/60 hover:text-white hover:bg-emerald-800/50 transition-all">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0" style="margin-left:16rem">
            <header class="sticky top-0 z-20 bg-white/80 dark:bg-navy-900/80 backdrop-blur-xl border-b border-gray-100 dark:border-navy-700">
                <div class="flex items-center justify-between h-16 px-4 lg:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-800">
                            <svg x-show="theme !== 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                    </div>
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
