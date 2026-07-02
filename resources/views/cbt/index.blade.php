<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Ujian CBT') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Available Exams -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ujian Tersedia</h3>
                    @forelse($exams as $exam)
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg mb-3 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $exam->title }}</h4>
                            <div class="flex items-center gap-4 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>📝 {{ $exam->question_count }} soal</span>
                                <span>⏱️ {{ $exam->duration_minutes }} menit</span>
                                <span>🎯 KKM {{ $exam->passing_score }}</span>
                            </div>
                        </div>
                        @if(isset($mySessions[$exam->id]) && in_array($mySessions[$exam->id]->status, ['finished', 'timeout']))
                            <a href="{{ route('cbt.result', $mySessions[$exam->id]->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Lihat Hasil</a>
                        @elseif(isset($mySessions[$exam->id]) && $mySessions[$exam->id]->status === 'in_progress')
                            <a href="{{ route('cbt.take', $mySessions[$exam->id]->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700">Lanjutkan</a>
                        @else
                            <form action="{{ route('cbt.start', $exam) }}" method="POST">
                                @csrf
                                <button class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Mulai</button>
                            </form>
                        @endif
                    </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Tidak ada ujian yang tersedia saat ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="text-right">
                <a href="{{ route('cbt.history') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">📋 Riwayat Ujian →</a>
            </div>
        </div>
    </div>
</x-app-layout>
