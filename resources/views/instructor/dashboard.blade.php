@extends('layouts.instructor')
@section('title', 'Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Instruktur</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan aktivitas mengajar Anda.</p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl mb-1">👨‍🏫</div>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['total_kelas'] }}</p>
        <p class="text-xs text-gray-500">Kelas Diampu</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl mb-1">👨‍🎓</div>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_siswa'] }}</p>
        <p class="text-xs text-gray-500">Total Siswa</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl mb-1">📚</div>
        <p class="text-2xl font-bold text-purple-600">{{ $stats['total_materi'] }}</p>
        <p class="text-xs text-gray-500">Materi</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl mb-1">📝</div>
        <p class="text-2xl font-bold text-orange-600">{{ $stats['total_tugas'] }}</p>
        <p class="text-xs text-gray-500">Tugas</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl mb-1">📅</div>
        <p class="text-2xl font-bold text-teal-600">{{ $stats['jadwal_hari_ini'] }}</p>
        <p class="text-xs text-gray-500">Jadwal Hari Ini</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl mb-1">💻</div>
        <p class="text-2xl font-bold text-rose-600">{{ $stats['ujian_aktif'] }}</p>
        <p class="text-xs text-gray-500">Ujian Aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Kelas Saya --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900 dark:text-white">Kelas Saya</h3>
            <a href="{{ route('instructor.classes') }}" class="text-xs text-emerald-600 hover:text-emerald-800">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($myClasses as $class)
            <a href="{{ route('instructor.classes.detail', $class) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-750">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $class->name }}</p>
                    <p class="text-xs text-gray-500">{{ $class->students_count }} siswa · {{ $class->materials_count }} materi · {{ $class->assignments_count }} tugas</p>
                </div>
                <span class="text-gray-400">→</span>
            </a>
            @empty
            <p class="px-6 py-6 text-center text-gray-500 text-sm">Belum ada kelas yang diampu.</p>
            @endforelse
        </div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Jadwal Hari Ini ({{ now()->locale('id')->dayName }})</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($todaySchedules as $s)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $s->schoolClass->name }}</p>
                    <p class="text-xs text-gray-500">{{ $s->room ?? 'Ruangan belum ditentukan' }}</p>
                </div>
                <span class="text-sm font-medium text-emerald-600">{{ date('H:i', strtotime($s->start_time)) }} - {{ date('H:i', strtotime($s->end_time)) }}</span>
            </div>
            @empty
            <p class="px-6 py-6 text-center text-gray-500 text-sm">Tidak ada jadwal hari ini.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Absensi Terbaru --}}
<div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white">Absensi Siswa Terbaru</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700">
            <tr><th class="px-4 py-2 text-left">Siswa</th><th class="px-4 py-2">Tipe</th><th class="px-4 py-2">Waktu</th></tr>
        </thead>
        <tbody>
            @forelse($recentAttendances as $a)
            <tr class="border-t dark:border-gray-700">
                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white text-xs">{{ $a->user->name }}</td>
                <td class="px-4 py-2 text-center text-xs">{!! $a->type === 'check_in' ? '<span class="text-green-600">✅ Masuk</span>' : '<span class="text-red-600">🚪 Pulang</span>' !!}</td>
                <td class="px-4 py-2 text-center text-xs text-gray-500">{{ $a->created_at->format('d/m H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500 text-xs">Belum ada data absensi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
