@extends('layouts.admin')
@section('title', 'Ujian CBT')
@section('content')

@php
    $bankTIU = \App\Models\Question::whereNull('exam_id')->where('category', 'TIU')->count();
    $bankTWK = \App\Models\Question::whereNull('exam_id')->where('category', 'TWK')->count();
    $bankTKP = \App\Models\Question::whereNull('exam_id')->where('category', 'TKP')->count();
    $canGenerate = $bankTWK >= 30 && $bankTIU >= 35 && $bankTKP >= 45;
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Ujian</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola ujian Computer Based Test</p>
    </div>
    <div class="flex items-center gap-2">
        <form action="{{ route('admin.exams.generate-tryout') }}" method="POST" onsubmit="return confirm('Generate Tryout SKD CPNS?\n\n110 soal (TWK:30, TIU:35, TKP:45)\n100 menit\n\nSoal diacak berbeda tiap peserta.')">
            @csrf
            <button type="submit" {{ !$canGenerate ? 'disabled' : '' }}
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-sm shadow-emerald-500/20"
                    title="{{ !$canGenerate ? 'Bank Soal belum mencukupi' : 'Generate Tryout SKD CPNS' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Generate Tryout SKD
            </button>
        </form>
        <a href="{{ route('admin.exams.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm shadow-indigo-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Buat Ujian
        </a>
    </div>
</div>

{{-- Bank Soal Status --}}
<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg border {{ $bankTWK >= 30 ? 'border-green-200 dark:border-green-800' : 'border-red-200 dark:border-red-800' }} px-4 py-2.5">
        <span class="text-xs text-gray-400">TWK</span>
        <div class="flex items-baseline gap-1"><span class="text-lg font-bold {{ $bankTWK >= 30 ? 'text-green-600' : 'text-red-500' }}">{{ $bankTWK }}</span><span class="text-xs text-gray-400">/30</span></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg border {{ $bankTIU >= 35 ? 'border-green-200 dark:border-green-800' : 'border-red-200 dark:border-red-800' }} px-4 py-2.5">
        <span class="text-xs text-gray-400">TIU</span>
        <div class="flex items-baseline gap-1"><span class="text-lg font-bold {{ $bankTIU >= 35 ? 'text-green-600' : 'text-red-500' }}">{{ $bankTIU }}</span><span class="text-xs text-gray-400">/35</span></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg border {{ $bankTKP >= 45 ? 'border-green-200 dark:border-green-800' : 'border-red-200 dark:border-red-800' }} px-4 py-2.5">
        <span class="text-xs text-gray-400">TKP</span>
        <div class="flex items-baseline gap-1"><span class="text-lg font-bold {{ $bankTKP >= 45 ? 'text-green-600' : 'text-red-500' }}">{{ $bankTKP }}</span><span class="text-xs text-gray-400">/45</span></div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">Judul</th>
                <th class="px-6 py-3">Tipe</th>
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
                <td class="px-6 py-3">
                    @if($exam->type === 'tryout_skd')
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Tryout SKD</span>
                    @elseif($exam->type === 'mini_quiz')
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Mini Quiz</span>
                    @else
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Reguler</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-gray-600">
                    @if($exam->type === 'tryout_skd')
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">110 soal</span>
                        <span class="text-[10px] text-gray-400 block">TWK:30 TIU:35 TKP:45</span>
                    @else
                        {{ $exam->question_count }} soal
                    @endif
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $exam->duration_minutes }} menit</td>
                <td class="px-6 py-3 text-gray-600">{{ $exam->passing_score }}</td>
                <td class="px-6 py-3 text-xs text-gray-500">{{ $exam->start_time->format('d/m/Y H:i') }} - {{ $exam->end_time->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-3">
                    @if($exam->is_available) <span class="text-green-600 dark:text-green-400 text-xs font-medium">🟢 Aktif</span>
                    @elseif(!$exam->is_active) <span class="text-gray-400 text-xs">⚫ Nonaktif</span>
                    @else <span class="text-amber-600 text-xs">🟡 Terjadwal</span> @endif
                </td>
                <td class="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                    @if($exam->type !== 'tryout_skd')
                    <a href="{{ route('admin.exams.questions', $exam) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-medium">Soal</a>
                    @endif
                    <a href="{{ route('admin.exams.results', $exam) }}" class="text-green-600 hover:text-green-800 dark:text-green-400 text-xs font-medium">Hasil</a>
                    <a href="{{ route('admin.exams.tokens', $exam) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 text-xs font-medium">Token</a>
                    <form action="{{ route('admin.exams.start-now', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Mulai ujian ini sekarang?')">
                        @csrf
                        <button class="text-amber-600 hover:text-amber-800 dark:text-amber-400 text-xs font-medium">▶ Mulai</button>
                    </form>
                    <a href="{{ route('admin.exams.edit', $exam) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-medium">Edit</a>
                    <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-medium">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500">Belum ada ujian.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">{{ $exams->links() }}</div>
</div>

@endsection
