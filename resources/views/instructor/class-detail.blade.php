@extends('layouts.instructor')
@section('title', $class->name)
@section('content')
<div class="mb-4"><a href="{{ route('instructor.classes') }}" class="text-sm text-emerald-600 hover:text-emerald-800">← Kembali ke Kelas Saya</a></div>
<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $class->name }}</h1>
<p class="text-gray-500 mb-6">Kode: {{ $class->code }} · Kapasitas: {{ $class->capacity }} · {{ $class->students->count() }} siswa</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Daftar Siswa --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3 border-b"><h3 class="font-semibold text-gray-900 dark:text-white text-sm">👨‍🎓 Daftar Siswa ({{ $class->students->count() }})</h3></div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-80 overflow-y-auto">
            @foreach($class->students as $student)
            <div class="flex items-center gap-3 px-5 py-2.5"><img src="{{ $student->avatar_url }}" class="w-7 h-7 rounded-full"><span class="text-sm text-gray-900 dark:text-white">{{ $student->name }}</span></div>
            @endforeach
        </div>
    </div>

    {{-- Materi --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3 border-b"><h3 class="font-semibold text-gray-900 dark:text-white text-sm">📚 Materi ({{ $class->materials->count() }})</h3></div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-80 overflow-y-auto">
            @forelse($class->materials as $m)
            <div class="px-5 py-2.5"><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $m->title }}</p></div>
            @empty
            <p class="px-5 py-4 text-xs text-gray-500">Belum ada materi.</p>
            @endforelse
        </div>
    </div>

    {{-- Tugas --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3 border-b"><h3 class="font-semibold text-gray-900 dark:text-white text-sm">📝 Tugas ({{ $class->assignments->count() }})</h3></div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-80 overflow-y-auto">
            @forelse($class->assignments as $a)
            <div class="px-5 py-2.5"><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $a->title }}</p><p class="text-xs text-gray-500">Deadline: {{ $a->due_date->format('d/m/Y H:i') }} · {{ $a->submissions->count() }} terkumpul</p></div>
            @empty
            <p class="px-5 py-4 text-xs text-gray-500">Belum ada tugas.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Absensi --}}
<div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-3 border-b"><h3 class="font-semibold text-gray-900 dark:text-white text-sm">📍 Riwayat Absensi</h3></div>
    <table class="w-full text-sm"><thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">Check-In</th><th class="px-4 py-2">Check-Out</th></tr></thead>
    <tbody>@forelse($attendances as $date => $items)<tr class="border-t dark:border-gray-700"><td class="px-4 py-2 font-medium text-xs">{{ \Carbon\Carbon::parse($date)->format('d M') }}</td><td class="px-4 py-2 text-xs">{{ $items->where('type','check_in')->count() }} siswa</td><td class="px-4 py-2 text-xs">{{ $items->where('type','check_out')->count() }} siswa</td></tr>@empty <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500 text-xs">Belum ada absensi.</td></tr>@endforelse</tbody></table>
</div>
@endsection
