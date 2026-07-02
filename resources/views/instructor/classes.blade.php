@extends('layouts.instructor')
@section('title', 'Kelas Saya')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">👨‍🏫 Kelas Saya</h1><p class="text-gray-500 mt-1">Daftar kelas yang Anda ampu beserta ringkasannya.</p></div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($classes as $class)
    <a href="{{ route('instructor.classes.detail', $class) }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700 transition">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $class->name }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Kode: {{ $class->code }}</p>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $class->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $class->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
        <div class="flex items-center gap-4 text-xs text-gray-500">
            <span>👨‍🎓 {{ $class->students_count }} siswa</span>
            <span>📚 {{ $class->materials_count }} materi</span>
            <span>📝 {{ $class->assignments_count }} tugas</span>
        </div>
    </a>
    @empty
    <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Belum ada kelas yang diampu.</div>
    @endforelse
</div>
@endsection
