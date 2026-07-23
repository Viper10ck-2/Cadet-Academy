<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">
    <title>{{ config('app.name', 'Cadet Academy') }} — Platform Akademi Modern</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}
    .hero-gradient{background:linear-gradient(135deg,#0F172A 0%,#1E3A8A 50%,#0F172A 100%)}
    .hero-glow{position:absolute;width:600px;height:600px;border-radius:50%;filter:blur(120px);opacity:.15;pointer-events:none}
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900" x-data="{ scrolled: false }"
      x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)">
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300" :class="scrolled ? 'bg-white/90 backdrop-blur-xl shadow-sm border-b border-gray-100' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-navy flex items-center justify-center text-gold font-extrabold text-sm shadow-lg">CA</div>
                    <span class="font-display font-bold text-lg" :class="scrolled ? 'text-navy' : 'text-white'">Cadet Academy</span>
                </a>
                <div class="flex items-center gap-4">
                    @auth
                        @php
                            $dashboardRoute = match(true) {
                                auth()->user()->hasRole('admin') => route('admin.dashboard'),
                                auth()->user()->hasRole('instructor') => route('instructor.dashboard'),
                                auth()->user()->hasRole('cadet') => route('cadet.dashboard'),
                                default => route('dashboard'),
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="bg-gold text-navy px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gold-600 transition-all shadow-lg shadow-gold/30 inline-flex items-center gap-2">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-navy' : 'text-white/80 hover:text-white'">Log in</a>
                        <a href="{{ route('register') }}" class="bg-gold text-navy px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gold-600 transition-all shadow-lg shadow-gold/30 inline-flex items-center gap-2">Mulai Gratis</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-gradient min-h-screen flex items-center relative overflow-hidden">
        <div class="hero-glow bg-gold" style="top:-200px;right:-200px"></div>
        <div class="hero-glow bg-blue-500" style="bottom:-300px;left:-100px"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-40 relative z-10">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold bg-gold/20 text-gold border border-gold/30 mb-6">🎓 Platform Pendidikan Modern</span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight">
                    Selamat Datang di<br><span class="text-gold">Cadet Academy</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-300 leading-relaxed max-w-2xl">
                    Platform akademi modern untuk pengembangan keterampilan dan pembelajaran. Bergabunglah dengan ribuan peserta dalam perjalanan menuju kesuksesan.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold text-navy font-bold px-8 py-3.5 rounded-xl text-base shadow-lg shadow-gold/20 hover:shadow-xl hover:shadow-gold/30 transition-all duration-200">🚀 Mulai Sekarang</a>
                    <a href="#features" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-base font-semibold text-white/80 hover:text-white border border-white/20 hover:bg-white/10 transition-all duration-200">Pelajari Lebih Lanjut <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></a>
                </div>
                <div class="mt-12 flex items-center gap-8 text-sm text-gray-400">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>100+ Cadet Aktif</span>
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>50+ Kelas</span>
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>10+ Instruktur Expert</span>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-20 lg:py-28 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-semibold uppercase tracking-widest text-gold-600 bg-gold-50 px-4 py-1.5 rounded-full">Fitur</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy mt-4">Mengapa Cadet Academy?</h2>
                <p class="text-gray-500 mt-4 text-lg">Platform lengkap untuk mendukung perjalanan belajar Anda</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-navy-50 flex items-center justify-center text-navy text-xl mb-5">📚</div>
                    <h3 class="font-display font-bold text-lg text-navy">Kurikulum Modern</h3>
                    <p class="text-gray-500 mt-2 leading-relaxed">Materi pembelajaran terkini yang disusun oleh para ahli di bidangnya.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-gold-50 flex items-center justify-center text-gold-600 text-xl mb-5">👨‍🏫</div>
                    <h3 class="font-display font-bold text-lg text-navy">Instruktur Expert</h3>
                    <p class="text-gray-500 mt-2 leading-relaxed">Dibimbing langsung oleh instruktur berpengalaman dan profesional.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl mb-5">🎯</div>
                    <h3 class="font-display font-bold text-lg text-navy">Sertifikasi</h3>
                    <p class="text-gray-500 mt-2 leading-relaxed">Dapatkan sertifikat resmi setelah menyelesaikan setiap program.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pejabat / Officials Section --}}
    <section id="pejabat" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-semibold uppercase tracking-widest text-navy-500 bg-navy-50 px-4 py-1.5 rounded-full">Struktur Organisasi</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy mt-4">Pejabat Cadet Academy</h2>
                <p class="text-gray-500 mt-4 text-lg">Dipimpin oleh para profesional berpengalaman di bidangnya</p>
            </div>

            @php
                $kepala = $officials->firstWhere('position', 'Kepala Cadet Academy');
                $sekretaris = $officials->firstWhere('position', 'Sekretaris');
                $kasubbags = $officials->filter(fn($o) => str_starts_with($o->position, 'Kasubbag'))->values();
                $bidangs = $officials->filter(fn($o) => str_contains($o->position, 'Bidang'))->values();
            @endphp

            {{-- Kepala --}}
            @if($kepala)
            <div class="flex justify-center mb-10">
                <div class="text-center max-w-xs animate-fade-up">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden ring-4 ring-gold/40 shadow-xl shadow-gold/10">
                            <img src="{{ $kepala->photo_url }}" alt="{{ $kepala->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-3 inset-x-0 flex justify-center">
                            <span class="bg-gold text-navy text-[10px] font-bold px-3 py-1 rounded-full shadow">⭐ Kepala</span>
                        </div>
                    </div>
                    <h3 class="font-display font-bold text-lg text-navy mt-6">{{ $kepala->name }}</h3>
                    <p class="text-gold-600 font-semibold text-xs mt-1">{{ $kepala->position }}</p>
                </div>
            </div>
            @endif

            {{-- Sekretaris --}}
            @if($sekretaris)
            <div class="flex justify-center mb-12">
                <div class="text-center max-w-xs animate-fade-up">
                    <div class="w-28 h-28 mx-auto rounded-2xl overflow-hidden ring-2 ring-gray-200 shadow-sm">
                        <img src="{{ $sekretaris->photo_url }}" alt="{{ $sekretaris->name }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-display font-bold text-base text-navy mt-4">{{ $sekretaris->name }}</h3>
                    <p class="text-gray-500 font-medium text-xs mt-1">{{ $sekretaris->position }}</p>
                </div>
            </div>
            @endif

            {{-- Kasubbag --}}
            @if($kasubbags->count() > 0)
            <div class="text-center mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 bg-gray-50 px-3 py-1 rounded-full">Kasubbag</span>
            </div>
            <div class="flex flex-wrap justify-center gap-6 lg:gap-12 mb-12">
                @foreach($kasubbags as $official)
                <div class="text-center max-w-[180px] animate-fade-up">
                    <div class="w-24 h-24 mx-auto rounded-2xl overflow-hidden ring-2 ring-gray-100 shadow-sm">
                        <img src="{{ $official->photo_url }}" alt="{{ $official->name }}" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-semibold text-sm text-navy mt-3 leading-tight">{{ $official->name }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $official->position }}</p>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Bidang --}}
            @if($bidangs->count() > 0)
            <div class="text-center mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 bg-gray-50 px-3 py-1 rounded-full">Bidang</span>
            </div>
            <div class="flex flex-wrap justify-center gap-6 lg:gap-8">
                @foreach($bidangs as $official)
                <div class="text-center max-w-[180px] animate-fade-up">
                    <div class="w-24 h-24 mx-auto rounded-2xl overflow-hidden ring-2 ring-gray-100 shadow-sm">
                        <img src="{{ $official->photo_url }}" alt="{{ $official->name }}" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-semibold text-sm text-navy mt-3 leading-tight">{{ $official->name }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $official->position }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <section class="py-20 lg:py-28 bg-navy relative overflow-hidden">
        <div class="hero-glow bg-gold" style="top:-200px;right:-200px"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">Siap Memulai Perjalanan Belajar?</h2>
            <p class="text-gray-400 mt-4 text-lg max-w-xl mx-auto">Bergabunglah dengan Cadet Academy dan akses ribuan materi pembelajaran berkualitas</p>
            <div class="mt-10"><a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold text-navy font-bold px-10 py-4 rounded-xl text-base shadow-lg shadow-gold/20 hover:shadow-xl hover:shadow-gold/30 transition-all duration-200">Daftar Sekarang — Gratis!</a></div>
        </div>
    </section>

    <footer class="bg-navy-900 border-t border-navy-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gold/20 flex items-center justify-center text-gold font-bold text-xs">CA</div>
                    <span class="font-display font-bold text-white">Cadet Academy</span>
                </div>
                <p class="text-gray-500 text-sm">© {{ date('Y') }} Cadet Academy. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
