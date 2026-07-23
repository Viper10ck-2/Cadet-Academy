<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0F172A">

        <title>{{ config('app.name', 'Cadet Academy') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important}
        .auth-glow{position:absolute;width:500px;height:500px;border-radius:50%;filter:blur(120px);opacity:.15;pointer-events:none}
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-navy via-navy-900 to-navy-950 relative overflow-hidden">
            <!-- Glow effects -->
            <div class="auth-glow bg-gold" style="top:-150px;right:-100px"></div>
            <div class="auth-glow bg-blue-500" style="bottom:-200px;left:-100px"></div>

            <!-- Logo -->
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-extrabold text-xl shadow-lg shadow-gold/30">
                        CA
                    </div>
                    <span class="font-display font-bold text-lg text-white/90">Cadet Academy</span>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md px-6 sm:px-0 animate-fade-in-up">
                <div class="bg-white/95 dark:bg-navy-800/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/20 border border-white/20 dark:border-navy-700/50 p-8">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-8 text-xs text-white/30">&copy; {{ date('Y') }} Cadet Academy. All rights reserved.</p>
        </div>
    </body>
</html>
