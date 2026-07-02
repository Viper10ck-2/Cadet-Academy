<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Notifikasi') }}</h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Tandai semua dibaca</button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @forelse($notifications as $notif)
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-start gap-3 {{ is_null($notif->read_at) ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                    <div class="flex-shrink-0 mt-1">
                        @if(is_null($notif->read_at))
                            <div class="w-2.5 h-2.5 bg-indigo-600 rounded-full"></div>
                        @else
                            <div class="w-2.5 h-2.5 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $notif->data['message'] ?? 'Notifikasi' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(is_null($notif->read_at))
                    <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST">
                        @csrf
                        <button class="text-xs text-indigo-600 hover:text-indigo-800">Tandai dibaca</button>
                    </form>
                    @endif
                </div>
                @empty
                <div class="px-6 py-10 text-center text-gray-500">Tidak ada notifikasi.</div>
                @endforelse
                <div class="px-6 py-4">{{ $notifications->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
