@extends('layouts.instructor')
@section('title', 'Jadwal Mengajar')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📅 Jadwal Mengajar</h1><p class="text-gray-500 mt-1">Kalender dan daftar kelas yang Anda ampu.</p></div>

@php $days = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu']; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($days as $day)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border-b border-emerald-100 dark:border-emerald-800">
            <h3 class="font-semibold text-emerald-800 dark:text-emerald-300 text-sm capitalize">{{ $day }}</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($schedules[$day] ?? [] as $s)
            <div class="px-4 py-2.5">
                <p class="font-medium text-gray-900 dark:text-white text-xs">{{ $s->schoolClass->name }}</p>
                <p class="text-xs text-gray-500">{{ date('H:i', strtotime($s->start_time)) }} - {{ date('H:i', strtotime($s->end_time)) }} @if($s->room) · {{ $s->room }} @endif</p>
            </div>
            @empty
            <p class="px-4 py-4 text-center text-gray-400 text-xs">—</p>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endsection
