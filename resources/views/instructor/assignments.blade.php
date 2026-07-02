@extends('layouts.instructor')
@section('title', 'Tugas & Penilaian')
@section('content')
<div class="flex items-center justify-between mb-6"><div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Tugas & Penilaian</h1><p class="text-gray-500 mt-1">Membuat tugas, memeriksa jawaban, dan memberikan nilai.</p></div>
<a href="{{ route('admin.akademik.tugas.create') }}" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ Buat Tugas</a></div>

<div class="space-y-4">
    @forelse($assignments as $assignment)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5" x-data="{ open: false }">
        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $assignment->title }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $assignment->schoolClass->name }} · Deadline: {{ $assignment->due_date->format('d/m/Y H:i') }} · Nilai Max: {{ $assignment->max_score }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ $assignment->submissions->count() }} terkumpul</span>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        <div x-show="open" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            @if($assignment->submissions->count() > 0)
            <table class="w-full text-sm"><thead class="text-xs text-gray-500 uppercase"><tr><th class="text-left py-1">Siswa</th><th class="py-1">File</th><th class="py-1">Nilai</th><th class="py-1">Status</th><th class="py-1">Aksi</th></tr></thead>
            <tbody>@foreach($assignment->submissions as $sub)<tr class="border-t dark:border-gray-700"><td class="py-2 text-xs font-medium text-gray-900 dark:text-white">{{ $sub->student->name }}</td><td class="py-2 text-center text-xs">{{ $sub->file_path ? '📎' : '📝' }}</td><td class="py-2 text-center text-xs font-semibold">{{ $sub->score ?? '-' }}</td><td class="py-2 text-center text-xs">{!! $sub->status === 'graded' ? '<span class="text-green-600">✅ Dinilai</span>' : '<span class="text-amber-600">⏳ Menunggu</span>' !!}</td><td class="py-2 text-center"><button @click.stop="$dispatch('open-modal','grade-{{ $sub->id }}')" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">Beri Nilai</button></td></tr>@endforeach</tbody></table>
            @else <p class="text-xs text-gray-500 text-center py-4">Belum ada yang mengumpulkan.</p> @endif
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Belum ada tugas.</div>
    @endforelse
</div>
@endsection
