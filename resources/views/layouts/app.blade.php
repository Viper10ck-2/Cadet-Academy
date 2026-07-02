<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0F172A">

        <title>@yield('title', config('app.name', 'Cadet Academy')) | {{ config('app.name', 'Cadet Academy') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ theme: localStorage.getItem('theme') || 'light', sidebarOpen: window.innerWidth >= 1024 }"
          x-init="$watch('theme', val => { document.documentElement.classList.toggle('dark', val === 'dark'); localStorage.setItem('theme', val); })"
          :class="theme === 'dark' ? 'dark' : ''"
          class="h-full font-sans antialiased bg-gray-50 text-gray-900 dark:bg-navy dark:text-gray-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="border-b border-gray-100 dark:border-navy-700 bg-white/80 backdrop-blur-xl dark:bg-navy-900/80 sticky top-0 z-20">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
