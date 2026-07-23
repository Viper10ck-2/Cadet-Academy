<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-gold-50 dark:bg-gold-900/20 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h1 class="font-display text-xl font-bold text-navy dark:text-white">Verifikasi Email</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Link verifikasi telah dikirim ke <strong class="text-navy dark:text-white">{{ $email ?? 'email Anda' }}</strong>.
            Silakan cek inbox atau spam folder Anda.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-sm font-medium border border-emerald-200 dark:border-emerald-800 text-center">
            Link verifikasi baru telah dikirim. Cek email Anda.
        </div>
    @endif

    <div class="space-y-3">
        <p class="text-center text-sm text-gray-400 dark:text-gray-500">
            Buka email Anda dan klik link verifikasi untuk mengaktifkan akun. Setelah itu, silakan login.
        </p>
        <a href="{{ route('login') }}" class="block w-full text-center px-5 py-3 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold rounded-xl font-bold text-sm text-navy shadow-lg shadow-gold/20 transition-all">
            Ke Halaman Login
        </a>
    </div>
</x-guest-layout>
