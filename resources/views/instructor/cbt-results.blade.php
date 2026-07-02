@extends('layouts.instructor')
@section('title', 'Hasil Ujian')
@section('content')
<div class="mb-4"><a href="{{ route('instructor.cbt') }}" class="text-sm text-emerald-600 hover:text-emerald-800">← Kembali</a></div>
<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Hasil: {{ $exam->title }}</h1>
<p class="text-gray-500 mb-6">KKM: {{ $exam->passing_score }} · {{ $sessions->total() }} peserta</p>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm"><thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3">#</th><th class="px-6 py-3">Nama</th><th class="px-6 py-3">Nilai</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Selesai</th></tr></thead>
    <tbody>@foreach($sessions as $s)<tr class="border-t dark:border-gray-700"><td class="px-6 py-3 text-xs">{{ $loop->iteration }}</td><td class="px-6 py-3 font-medium text-sm">{{ $s->user->name }}</td><td class="px-6 py-3 font-bold {{ $s->is_passed ? 'text-green-600' : 'text-red-600' }}">{{ $s->score }}</td><td class="px-6 py-3">{!! $s->is_passed ? '<span class="text-green-600 text-xs">✅ Lulus</span>' : '<span class="text-red-600 text-xs">❌ Tidak Lulus</span>' !!}</td><td class="px-6 py-3 text-xs text-gray-500">{{ $s->finished_at->format('d/m H:i') }}</td></tr>@endforeach</tbody></table>
    <div class="px-6 py-4">{{ $sessions->links() }}</div>
</div>
@endsection
