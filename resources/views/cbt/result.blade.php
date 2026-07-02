<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Hasil Ujian') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8">
                <div class="text-center mb-8">
                    <div class="text-6xl mb-4">{{ $session->is_passed ? '🎉' : '😔' }}</div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">
                        {{ $session->status === 'timeout' ? 'Waktu pengerjaan habis' : 'Ujian selesai' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai</p>
                        <p class="text-4xl font-bold {{ $session->is_passed ? 'text-green-600' : 'text-red-600' }}">{{ $session->score }}</p>
                        <p class="text-xs text-gray-500 mt-1">KKM: {{ $exam->passing_score }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jawaban Benar</p>
                        <p class="text-4xl font-bold text-indigo-600">{{ $session->correct_answers }}/{{ $session->answered_questions }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total soal: {{ $session->total_questions }}</p>
                    </div>
                </div>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('cbt.index') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">Kembali ke Ujian</a>
                    <a href="{{ route('cbt.history') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium">Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
