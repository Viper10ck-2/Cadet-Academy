@extends('layouts.admin')
@section('title', 'Hasil - ' . $exam->title)
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hasil Ujian: {{ $exam->title }}</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">KKM: {{ $exam->passing_score }} | Total sesi selesai: {{ $sessions->total() }}</p>
    <a href="{{ route('admin.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">← Kembali</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Nilai</th>
                <th class="px-6 py-3">Benar</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $s)
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-3 text-gray-500">{{ $loop->iteration }}</td>
                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $s->user->name }}</td>
                <td class="px-6 py-3">
                    <span class="text-lg font-bold {{ $s->is_passed ? 'text-green-600' : 'text-red-600' }}">{{ $s->score }}</span>
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $s->correct_answers }}/{{ $s->answered_questions }}</td>
                <td class="px-6 py-3">{!! $s->is_passed ? '<span class="text-green-600 font-medium text-xs">✅ Lulus</span>' : '<span class="text-red-600 font-medium text-xs">❌ Tidak Lulus</span>' !!}</td>
                <td class="px-6 py-3 text-gray-500 text-xs">{{ $s->finished_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">{{ $sessions->links() }}</div>
</div>

@endsection
