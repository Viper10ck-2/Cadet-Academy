<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Riwayat Ujian') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-3">Ujian</th>
                            <th class="px-6 py-3">Nilai</th>
                            <th class="px-6 py-3">Hasil</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $s)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $s->exam->title }}</td>
                            <td class="px-6 py-3 font-bold {{ $s->is_passed ? 'text-green-600' : 'text-red-600' }}">{{ $s->score }}</td>
                            <td class="px-6 py-3">{!! $s->is_passed ? '<span class="text-green-600 text-xs font-medium">✅ Lulus</span>' : '<span class="text-red-600 text-xs font-medium">❌ Tidak Lulus</span>' !!}</td>
                            <td class="px-6 py-3 text-xs text-gray-500">{{ $s->finished_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3"><a href="{{ route('cbt.result', $s) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Detail</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada riwayat ujian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t">{{ $sessions->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
