<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">
    <title>Edit Profile | Cadet Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen flex flex-col bg-gray-50 dark:bg-navy text-gray-900 dark:text-gray-100 font-sans antialiased"
      x-data="{ theme: localStorage.getItem('theme') || 'light' }"
      x-init="document.documentElement.classList.toggle('dark', theme === 'dark')"
      :class="theme === 'dark' ? 'dark' : ''">

    <!-- Compact Topbar -->
    <header class="shrink-0 bg-white dark:bg-navy-900 border-b border-gray-100 dark:border-navy-700">
        <div class="max-w-5xl mx-auto px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-navy hover:bg-gray-100 dark:hover:text-white dark:hover:bg-navy-800 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-base font-bold text-navy dark:text-white">Edit Profile</h1>
            </div>
            <button @click="theme = theme === 'dark' ? 'light' : 'dark'; document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', theme)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-800 transition-all">
                <svg x-show="theme !== 'dark'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-show="theme === 'dark'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto">
        <div class="max-w-5xl mx-auto px-6 py-6">

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                     class="mb-4 px-4 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-sm font-medium border border-emerald-200 dark:border-emerald-800">
                    Profil berhasil diperbarui.
                </div>
            @endif
            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                     class="mb-4 px-4 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-sm font-medium border border-emerald-200 dark:border-emerald-800">
                    Password berhasil diubah.
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
                <!-- Left: Profil Info -->
                <div class="lg:col-span-3 bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-6">
                    <h3 class="text-sm font-bold text-navy dark:text-white mb-5">Informasi Profil</h3>
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="flex items-center gap-4">
                            <div class="relative group shrink-0">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" class="w-16 h-16 rounded-2xl object-cover shadow-md">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-extrabold text-xl shadow-md">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <label class="absolute -bottom-1 -right-1 w-6 h-6 bg-navy dark:bg-white rounded-lg flex items-center justify-center cursor-pointer hover:scale-110 transition-transform shadow-lg">
                                    <svg class="w-3 h-3 text-white dark:text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                                </label>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-navy dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Nama</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                       class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 focus:border-gold focus:ring-gold/30 rounded-lg px-3.5 py-2 text-sm transition-all">
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                       class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 focus:border-gold focus:ring-gold/30 rounded-lg px-3.5 py-2 text-sm transition-all">
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold rounded-lg font-bold text-xs text-navy shadow-md shadow-gold/20 hover:shadow-gold/30 transition-all">
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Ganti Password -->
                <div class="lg:col-span-2 bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-6">
                    <h3 class="text-sm font-bold text-navy dark:text-white mb-1">Ganti Password</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-5">Gunakan password yang kuat</p>

                    <form method="post" action="{{ route('profile.password.update') }}" class="space-y-3.5">
                        @csrf
                        @method('put')

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" required autocomplete="current-password"
                                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 focus:border-gold focus:ring-gold/30 rounded-lg px-3.5 py-2 text-sm transition-all">
                            <x-input-error :messages="$errors->get('current_password')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Password Baru</label>
                            <input type="password" name="password" required autocomplete="new-password"
                                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 focus:border-gold focus:ring-gold/30 rounded-lg px-3.5 py-2 text-sm transition-all">
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full border-gray-200 dark:border-navy-600 dark:bg-navy-900/50 dark:text-gray-200 focus:border-gold focus:ring-gold/30 rounded-lg px-3.5 py-2 text-sm transition-all">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>

                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gradient-to-r from-gold to-gold-500 hover:from-gold-600 hover:to-gold rounded-lg font-bold text-xs text-navy shadow-md shadow-gold/20 hover:shadow-gold/30 transition-all mt-1">
                            Ganti Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
