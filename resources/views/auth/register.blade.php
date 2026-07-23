<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="font-display text-xl font-bold text-navy dark:text-white">Daftar</h1>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Buat akun Cadet Academy</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus autocomplete="name"
                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-gold focus:ring-gold/30 rounded-xl px-4 py-2.5 text-sm transition-all">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>
        <div>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="username"
                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-gold focus:ring-gold/30 rounded-xl px-4 py-2.5 text-sm transition-all">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>
        <div>
            <input type="password" name="password" placeholder="Password (min. 8 karakter)" required autocomplete="new-password"
                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-gold focus:ring-gold/30 rounded-xl px-4 py-2.5 text-sm transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
        <div>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required autocomplete="new-password"
                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-gold focus:ring-gold/30 rounded-xl px-4 py-2.5 text-sm transition-all">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>
        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold rounded-xl font-bold text-sm text-navy shadow-md shadow-gold/20 transition-all">
            Daftar
        </button>
        <p class="text-center text-xs text-gray-400 dark:text-gray-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-gold-600 dark:text-gold-400 font-semibold hover:text-gold">Masuk</a>
        </p>
    </form>
</x-guest-layout>
