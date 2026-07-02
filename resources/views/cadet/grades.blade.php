@extends('layouts.cadet')
@section('title','Nilai & Progress')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📊 Nilai & Progress</h1><p class="text-gray-500 mt-1">Lihat nilai, perkembangan, dan pencapaian belajar Anda.</p></div>

{{-- Nilai Ujian --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden mb-6">
    <div class="px-6 py-3 border-b"><h3 class="font-semibold text-gray-900 dark:text-white">💻 Nilai Try Out</h3></div>
    <table class="w-full text-sm"><thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3">Ujian</th><th class="px-4 py-3">Nilai</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Tanggal</th></tr></thead>
    <tbody>@forelse($examGrades as $g)<tr class="border-t dark:border-gray-700"><td class="px-4 py-3 font-medium text-xs">{{ $g->exam->title }}</td><td class="px-4 py-3 font-bold {{ $g->score>=$g->exam->passing_score ? 'text-green-600':'text-red-600' }}">{{ $g->score }}</td><td class="px-4 py-3">{!! $g->score>=$g->exam->passing_score ? '<span class="text-xs text-green-600">✅ Lulus</span>' : '<span class="text-xs text-red-500">❌ Remedial</span>' !!}</td><td class="px-4 py-3 text-xs text-gray-500">{{ $g->finished_at?->format('d/m/Y') }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">Belum ada nilai ujian.</td></tr>@endforelse</tbody></table>
</div>

{{-- Nilai Tugas Per Kelas --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($myClasses as $class)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-3 border-b bg-blue-50 dark:bg-blue-900/20"><h3 class="font-semibold text-blue-800 dark:text-blue-300 text-sm">{{ $class->name }}</h3></div>
        <div class="divide-y">@forelse($class->assignments as $a)<div class="flex justify-between px-5 py-2.5"><span class="text-xs text-gray-700 dark:text-gray-300">{{ $a->title }}</span><span class="text-xs font-bold">{{ $a->submissions->first()?->score ?? '—' }} / {{ $a->max_score }}</span></div>@empty<p class="px-5 py-4 text-xs text-gray-500">Belum ada tugas.</p>@endforelse</div>
    </div>
    @endforeach
</div>
@endsection
