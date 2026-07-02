<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Absen Academy">
    <link rel="manifest" href="/absen-manifest.json">
    <link rel="apple-touch-icon" href="/icons/absen-192.png">
    <title>@yield('title','Absensi') | Cadet Academy</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}
    .navy-gradient{background:linear-gradient(135deg,#0F172A 0%,#1E3A5F 50%,#0F172A 100%)}
    .gold-gradient{background:linear-gradient(135deg,#D4A853 0%,#F0C75E 50%,#D4A853 100%)}
    .gold-text{color:#D4A853}
    .gold-border{border-color:#D4A853}
    .btn-gold{background:linear-gradient(135deg,#D4A853,#F0C75E);color:#0F172A;font-weight:700}
    .btn-gold:hover{background:linear-gradient(135deg,#C49A43,#E0B74E)}
    .bottom-nav{position:fixed;bottom:0;left:0;right:0;z-index:50;background:#0F172A;border-top:1px solid #1E3A5F;padding:8px 0 env(safe-area-inset-bottom);}
    .pulse{animation:pulse 2s infinite}@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#0F172A] text-white font-sans antialiased" x-data>
<div class="flex flex-col min-h-full pb-16">
    @yield('content')
</div>
{{-- Bottom Navigation --}}
@auth
<nav class="bottom-nav">
    <div class="max-w-lg mx-auto flex items-center justify-around px-4">
        <a href="{{ route('absen.dashboard') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('absen.dashboard') ? 'text-[#D4A853]' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        <a href="{{ route('absen.history') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('absen.history') ? 'text-[#D4A853]' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            <span class="text-[10px] font-medium">Riwayat</span>
        </a>
        <a href="{{ route('absen.profile') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('absen.profile') ? 'text-[#D4A853]' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v1.2c0 .7.5 1.2 1.2 1.2h16.8c.7 0 1.2-.5 1.2-1.2v-1.2c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </div>
</nav>
@endauth
@stack('scripts')
{{-- PWA Service Worker --}}
<script>if('serviceWorker'in navigator){navigator.serviceWorker.register('/absen-sw.js')}</script>
</body>
</html>
