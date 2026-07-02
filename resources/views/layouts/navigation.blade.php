<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
     :class="scrolled ? 'shadow-sm border-b border-gray-200/50 dark:border-navy-700/50' : 'border-b border-transparent'"
     class="fixed top-0 inset-x-0 z-50 bg-white/90 dark:bg-navy-900/90 backdrop-blur-xl transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-navy to-navy-700 dark:from-gold dark:to-gold-600 flex items-center justify-center text-white dark:text-navy font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow">
                        CA
                    </div>
                    <span class="font-display font-bold text-lg text-navy dark:text-white">{{ config('app.name', 'Cadet Academy') }}</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-navy-50 text-navy dark:bg-navy-800 dark:text-gold' : 'text-gray-600 dark:text-gray-400 hover:text-navy dark:hover:text-white hover:bg-gray-50 dark:hover:bg-navy-800' }} transition-all duration-200">
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Right Side -->
            @auth
            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <button @click="theme = theme === 'dark' ? 'light' : 'dark'"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:text-navy hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-navy-700 transition-all duration-200">
                    <svg x-show="theme !== 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-800 transition-all duration-200 group">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-bold text-xs shadow-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-navy dark:group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-navy-800 rounded-2xl shadow-xl shadow-black/5 border border-gray-100 dark:border-navy-700 py-1.5 z-50">
                        <div class="px-4 py-2.5 border-b border-gray-50 dark:border-navy-700 mb-1">
                            <p class="text-sm font-semibold text-navy dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-navy dark:hover:text-white transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary text-sm">Register</a>
            </div>
            @endauth

            <!-- Mobile Hamburger -->
            <div class="sm:hidden flex items-center">
                <button @click="open = !open" class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-800 transition-all">
                    <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-collapse class="sm:hidden border-t border-gray-100 dark:border-navy-700 bg-white dark:bg-navy-900">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-navy-50 text-navy dark:bg-navy-800 dark:text-gold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-navy-800' }}">Dashboard</a>
        </div>
        @auth
        <div class="border-t border-gray-100 dark:border-navy-700 px-4 py-3">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-gold to-gold-500 flex items-center justify-center text-navy font-bold text-xs">CA</div>
                <div>
                    <p class="text-sm font-semibold text-navy dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-800">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">Log Out</button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
