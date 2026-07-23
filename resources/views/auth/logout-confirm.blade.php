<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">
    <title>Logout | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-navy via-navy-900 to-navy-950 flex items-center justify-center">
    <div class="text-center animate-fade-in-up">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-extrabold text-xl shadow-lg shadow-gold/30 mx-auto mb-6">
            CA
        </div>
        <h1 class="font-display text-2xl font-bold text-white mb-2">Logout</h1>
        <p class="text-gray-400 mb-6">Apakah Anda yakin ingin keluar?</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold text-navy font-bold px-8 py-3 rounded-xl shadow-lg shadow-gold/20 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Ya, Logout
            </button>
        </form>
        <p class="mt-4">
            <a href="{{ url()->previous() }}" class="text-sm text-gray-400 hover:text-white transition-colors">Batal</a>
        </p>
    </div>
</body>
</html>
