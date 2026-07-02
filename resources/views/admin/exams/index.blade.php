@extends('layouts.admin')
@section('title', 'Ujian CBT')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Ujian</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola ujian Computer Based Test</p>
    </div>
    <a href="{{ route('admin.exams.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Buat Ujian
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">Judul</th>
                <th class="px-6 py-3">Soal</th>
                <th class="px-6 py-3">Durasi</th>
                <th class="px-6 py-3">KKM</th>
                <th class="px-6 py-3">Periode</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exams as $exam)
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $exam->title }}</td>
                <td class="px-6 py-3 text-gray-600">{{ $exam->question_count }} soal</td>
                <td class="px-6 py-3 text-gray-600">{{ $exam->duration_minutes }} menit</td>
                <td class="px-6 py-3 text-gray-600">{{ $exam->passing_score }}</td>
                <td class="px-6 py-3 text-xs text-gray-500">{{ $exam->start_time->format('d/m/Y H:i') }} - {{ $exam->end_time->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-3">
                    @if($exam->is_available) <span class="text-green-600 dark:text-green-400 text-xs font-medium">🟢 Aktif</span>
                    @elseif(!$exam->is_active) <span class="text-gray-400 text-xs">⚫ Nonaktif</span>
                    @else <span class="text-amber-600 text-xs">🟡 Terjadwal</span> @endif
                </td>
                <td class="px-6 py-3 text-right space-x-2">
                    <a href="{{ route('admin.exams.questions', $exam) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-medium">Soal</a>
                    <a href="{{ route('admin.exams.results', $exam) }}" class="text-green-600 hover:text-green-800 dark:text-green-400 text-xs font-medium">Hasil</a>
                    <a href="{{ route('admin.exams.edit', $exam) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-medium">Edit</a>
                    <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-medium">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">Belum ada ujian.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">{{ $exams->links() }}</div>
</div>

@endsection
