<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="font-display text-xl font-bold text-navy dark:text-white">Masuk</h1>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Masuk ke akun Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 px-3 py-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-xs font-medium border border-emerald-200 dark:border-emerald-800 text-center">
            Akun berhasil dibuat! Cek email untuk verifikasi.
        </div>
    @endif
    @if (session('status') == 'email-verified')
        <div class="mb-4 px-3 py-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-xs font-medium border border-emerald-200 dark:border-emerald-800 text-center">
            Email terverifikasi! Silakan login.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username"
                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-gold focus:ring-gold/30 rounded-xl px-4 py-2.5 text-sm transition-all">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>
        <div>
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password"
                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-gold focus:ring-gold/30 rounded-xl px-4 py-2.5 text-sm transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
        <div class="flex items-center justify-between text-xs">
            <label class="inline-flex items-center gap-1.5 cursor-pointer text-gray-500 dark:text-gray-400">
                <input type="checkbox" name="remember" class="rounded-md border-gray-300 dark:border-navy-600 text-gold focus:ring-gold/30"> Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-gold-600 dark:text-gold-400 hover:text-gold font-medium">Lupa password?</a>
            @endif
        </div>
        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold rounded-xl font-bold text-sm text-navy shadow-md shadow-gold/20 transition-all">
            Masuk
        </button>
        @if (Route::has('register'))
            <p class="text-center text-xs text-gray-400 dark:text-gray-500">
                Belum punya akun? <a href="{{ route('register') }}" class="text-gold-600 dark:text-gold-400 font-semibold hover:text-gold">Daftar</a>
            </p>
        @endif
    </form>
</x-guest-layout>
