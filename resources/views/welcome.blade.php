<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cadet Academy</title>

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#4F46E5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Cadet Academy">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">🎓 Cadet Academy</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="ml-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">Register</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="flex-1 flex flex-col items-center justify-center px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-5xl sm:text-6xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Selamat Datang di
                        <span class="text-indigo-600 dark:text-indigo-400">Cadet Academy</span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                        Platform akademi modern untuk pengembangan keterampilan dan pembelajaran. 
                        Bergabunglah dengan ribuan peserta dalam perjalanan menuju kesuksesan.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-lg shadow-indigo-500/25">
                                Mulai Sekarang
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-lg font-semibold rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            Login
                        </a>
                    </div>
                </div>

                <!-- Features -->
                <div class="mt-20 max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="text-4xl mb-4">📚</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Kurikulum Modern</h3>
                        <p class="text-gray-600 dark:text-gray-400">Materi pembelajaran terkini yang disusun oleh para ahli di bidangnya.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="text-4xl mb-4">👨‍🏫</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Instruktur Expert</h3>
                        <p class="text-gray-600 dark:text-gray-400">Dibimbing langsung oleh instruktur berpengalaman dan profesional.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Sertifikasi</h3>
                        <p class="text-gray-600 dark:text-gray-400">Dapatkan sertifikat resmi setelah menyelesaikan setiap program.</p>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="mt-20 py-8 text-center text-gray-500 dark:text-gray-500 text-sm">
                &copy; {{ date('Y') }} Cadet Academy. All rights reserved.
            </footer>
        </div>

        <!-- PWA Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js');
            }
        </script>
    </body>
</html>
