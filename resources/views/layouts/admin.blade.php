<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">
    <title>@yield('title', 'Admin') | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-navy dark:text-gray-100 font-sans antialiased"
      x-data="{ theme: localStorage.getItem('theme') || 'light', sidebarOpen: window.innerWidth >= 1024, sidebarCollapsed: localStorage.getItem('sidebar') === 'collapsed' }"
      x-init="
          document.documentElement.classList.toggle('dark', theme === 'dark');
          $watch('theme', val => { document.documentElement.classList.toggle('dark', val === 'dark'); localStorage.setItem('theme', val); });
      "
      :class="theme === 'dark' ? 'dark' : ''"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">
    <div class="flex min-h-screen overflow-hidden">
        <!-- Overlay mobile -->
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden transition-opacity"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-navy text-white transition-all duration-300 ease-in-out shadow-2xl shadow-navy/20"
               :class="[
                   sidebarCollapsed ? 'w-20' : 'w-64',
                   sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                   'lg:translate-x-0'
               ]"
               x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebar', val ? 'collapsed' : 'expanded'))">
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-navy-700/50 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-extrabold text-sm shadow-lg shadow-gold/20 shrink-0">
                        CA
                    </div>
                    <span x-show="!sidebarCollapsed" class="font-display font-bold text-base truncate" x-transition>Cadet Academy</span>
                </a>
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="ml-auto w-7 h-7 rounded-lg flex items-center justify-center text-navy-400 hover:text-white hover:bg-navy-700 transition-all shrink-0 hidden lg:flex">
                    <svg class="w-4 h-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-thin">
                <p x-show="!sidebarCollapsed" class="px-3 py-1 text-[10px] font-semibold uppercase tracking-widest text-navy-400">Menu</p>

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="!sidebarCollapsed">Dashboard</span>
                </a>

                {{-- Akademik --}}
                <div x-data="{ open: {{ request()->is('admin/akademik*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">Akademik</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.akademik.kelas.index') }}" class="nav-sub {{ request()->routeIs('admin.akademik.kelas.*') ? 'nav-sub-active' : '' }}">Kelas</a>
                        <a href="{{ route('admin.akademik.jadwal.index') }}" class="nav-sub {{ request()->routeIs('admin.akademik.jadwal.*') ? 'nav-sub-active' : '' }}">Jadwal</a>
                        <a href="{{ route('admin.akademik.materi.index') }}" class="nav-sub {{ request()->routeIs('admin.akademik.materi.*') ? 'nav-sub-active' : '' }}">Materi</a>
                        <a href="{{ route('admin.akademik.tugas.index') }}" class="nav-sub {{ request()->routeIs('admin.akademik.tugas.*') ? 'nav-sub-active' : '' }}">Tugas</a>
                        <a href="{{ route('admin.akademik.sertifikat.index') }}" class="nav-sub {{ request()->routeIs('admin.akademik.sertifikat.*') ? 'nav-sub-active' : '' }}">Sertifikat</a>
                    </div>
                </div>

                {{-- CBT --}}
                <div x-data="{ open: {{ request()->is('admin/exams*') || request()->is('admin/questions*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">CBT</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.exams.index') }}" class="nav-sub {{ request()->routeIs('admin.exams.*') ? 'nav-sub-active' : '' }}">Ujian</a>
                        <a href="{{ route('admin.questions.index') }}" class="nav-sub {{ request()->routeIs('admin.questions.*') ? 'nav-sub-active' : '' }}">Bank Soal</a>
                    </div>
                </div>

                {{-- Absensi --}}
                <div x-data="{ open: {{ request()->is('admin/attendance*') || request()->is('admin/lokasi*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">Absensi</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.attendance') }}" class="nav-sub {{ request()->routeIs('admin.attendance') ? 'nav-sub-active' : '' }}">Kehadiran</a>
                        <a href="{{ route('admin.lokasi.index') }}" class="nav-sub {{ request()->routeIs('admin.lokasi.*') ? 'nav-sub-active' : '' }}">Lokasi</a>
                    </div>
                </div>

                {{-- Users --}}
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'nav-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    <span x-show="!sidebarCollapsed">Users</span>
                </a>

                {{-- Keuangan --}}
                <div x-data="{ open: {{ request()->is('admin/keuangan*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">Keuangan</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="nav-sub {{ request()->routeIs('admin.keuangan.tagihan.*') ? 'nav-sub-active' : '' }}">Tagihan</a>
                        <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="nav-sub {{ request()->routeIs('admin.keuangan.pembayaran.*') ? 'nav-sub-active' : '' }}">Pembayaran</a>
                    </div>
                </div>

                {{-- CRM --}}
                <div x-data="{ open: {{ request()->is('admin/crm*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">CRM & Marketing</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.crm.leads.index') }}" class="nav-sub {{ request()->routeIs('admin.crm.leads.*') ? 'nav-sub-active' : '' }}">Leads</a>
                        <a href="{{ route('admin.crm.kampanye.index') }}" class="nav-sub {{ request()->routeIs('admin.crm.kampanye.*') ? 'nav-sub-active' : '' }}">Kampanye</a>
                        <a href="{{ route('admin.crm.broadcast.index') }}" class="nav-sub {{ request()->routeIs('admin.crm.broadcast.*') ? 'nav-sub-active' : '' }}">Broadcast</a>
                        <a href="{{ route('admin.crm.testimoni.index') }}" class="nav-sub {{ request()->routeIs('admin.crm.testimoni.*') ? 'nav-sub-active' : '' }}">Testimoni</a>
                    </div>
                </div>

                {{-- Website --}}
                <div x-data="{ open: {{ request()->is('admin/website*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">Website</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.website.blog.index') }}" class="nav-sub {{ request()->routeIs('admin.website.blog.*') ? 'nav-sub-active' : '' }}">Blog</a>
                        <a href="{{ route('admin.website.faq.index') }}" class="nav-sub {{ request()->routeIs('admin.website.faq.*') ? 'nav-sub-active' : '' }}">FAQ</a>
                        <a href="{{ route('admin.website.banner.index') }}" class="nav-sub {{ request()->routeIs('admin.website.banner.*') ? 'nav-sub-active' : '' }}">Banner</a>
                        <a href="{{ route('admin.website.pejabat.index') }}" class="nav-sub {{ request()->routeIs('admin.website.pejabat.*') ? 'nav-sub-active' : '' }}">Pejabat</a>
                    </div>
                </div>

                {{-- Settings --}}
                <div x-data="{ open: {{ request()->is('admin/settings*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-item w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="flex-1">Pengaturan</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse :class="sidebarCollapsed ? 'hidden' : ''" class="ml-2 mt-0.5 space-y-0.5 border-l border-navy-600/50 pl-2">
                        <a href="{{ route('admin.settings.smtp.index') }}" class="nav-sub {{ request()->routeIs('admin.settings.smtp.*') ? 'nav-sub-active' : '' }}">Pengaturan</a>
                        <a href="{{ route('admin.settings.audit-log.index') }}" class="nav-sub {{ request()->routeIs('admin.settings.audit-log.*') ? 'nav-sub-active' : '' }}">Audit Log</a>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-3 border-t border-navy-700/50 shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-navy-300 hover:text-white hover:bg-navy-700/50 transition-all duration-200">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span x-show="!sidebarCollapsed" class="text-sm font-medium">Main Site</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300"
             :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">

            <!-- Topbar -->
            <header class="sticky top-0 z-20 bg-white/80 dark:bg-navy-900/80 backdrop-blur-xl border-b border-gray-100 dark:border-navy-700">
                <div class="flex items-center justify-between h-16 px-4 lg:px-6">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-800 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h2 class="text-lg font-semibold text-navy dark:text-white hidden sm:block">@yield('title','Dashboard')</h2>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Theme Toggle -->
                        <button @click="theme = theme === 'dark' ? 'light' : 'dark'"
                                x-init="if (!localStorage.getItem('theme')) theme = 'light'; $nextTick(() => document.documentElement.classList.toggle('dark', theme === 'dark'))"
                                class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:text-navy hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-navy-800 transition-all duration-200"
                                :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'">
                            <svg x-show="theme !== 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>

                        <!-- Notifications Popover -->
                        <div class="relative" x-data="{ open: false, unread: {{ auth()->user()->unreadNotifications->count() ?? 0 }} }" @click.away="open = false">
                            <button @click="open = !open"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:text-navy hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-navy-800 transition-all duration-200 relative">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span x-show="unread > 0" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-navy-800"></span>
                            </button>
                            <!-- Popover -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                                 class="absolute right-0 mt-3 w-80 bg-white dark:bg-navy-800 rounded-2xl shadow-xl shadow-black/10 border border-gray-100 dark:border-navy-700 z-50 overflow-hidden">
                                <div class="px-5 py-3.5 border-b border-gray-50 dark:border-navy-700 flex items-center justify-between">
                                    <h4 class="text-sm font-bold text-navy dark:text-white">Notifikasi</h4>
                                    <div class="flex items-center gap-2">
                                        <span x-show="unread > 0" class="text-[11px] font-medium text-gold-600 bg-gold-50 dark:bg-gold-900/20 px-2 py-0.5 rounded-full" x-text="unread + ' baru'"></span>
                                        <a href="{{ route('notifications.mark-all-read') }}" class="text-[11px] text-gray-400 hover:text-navy dark:hover:text-white transition-colors" @click.prevent="fetch('{{ route('notifications.mark-all-read') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => unread = 0)">Tandai semua</a>
                                    </div>
                                </div>
                                <div class="max-h-72 overflow-y-auto divide-y divide-gray-50 dark:divide-navy-700">
                                    @forelse(auth()->user()->notifications->take(5) as $notif)
                                    <a href="{{ $notif->data['url'] ?? '#' }}" class="flex gap-3 px-5 py-3.5 hover:bg-gray-50/50 dark:hover:bg-navy-700/50 transition-colors block">
                                        <div class="w-8 h-8 rounded-xl bg-navy-50 dark:bg-navy-700 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] font-medium text-navy dark:text-white leading-snug">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                            <p class="text-[12px] text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $notif->data['message'] ?? '' }}</p>
                                            <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(is_null($notif->read_at))
                                        <div class="w-2 h-2 rounded-full bg-gold-500 shrink-0 mt-2"></div>
                                        @endif
                                    </a>
                                    @empty
                                    <div class="px-5 py-8 text-center">
                                        <svg class="w-10 h-10 text-gray-200 dark:text-navy-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada notifikasi</p>
                                    </div>
                                    @endforelse
                                </div>
                                <div class="px-5 py-3 border-t border-gray-50 dark:border-navy-700 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-[13px] font-semibold text-gold-600 dark:text-gold-400 hover:text-gold transition-colors">Lihat semua notifikasi</a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-800 transition-all duration-200">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-bold text-xs shadow-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-navy dark:text-white leading-tight">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Admin</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-navy-800 rounded-2xl shadow-xl shadow-black/5 border border-gray-100 dark:border-navy-700 py-1.5 z-50">
                                <div class="px-4 py-2.5 border-b border-gray-50 dark:border-navy-700 mb-1">
                                    <p class="text-sm font-semibold text-navy dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 animate-fade-in">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>