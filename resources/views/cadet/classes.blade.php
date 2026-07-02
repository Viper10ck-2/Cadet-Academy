@extends('layouts.cadet')
@section('title','Kelas Saya')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📚 Kelas Saya</h1><p class="text-gray-500 mt-1">Daftar kelas yang Anda ikuti.</p></div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($classes as $class)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $class->name }}</h3>
        <p class="text-xs text-gray-500 mt-1">Kode: {{ $class->code }}</p>
        <div class="flex items-center gap-2 mt-3 text-xs text-gray-500">
            <span>👨‍🏫 {{ $class->instructor->name ?? '-' }}</span>
        </div>
        @if($class->schedules->count()>0)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs font-medium text-gray-500 mb-1">Jadwal:</p>
            @foreach($class->schedules as $s)
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ ucfirst($s->day) }} {{ date('H:i',strtotime($s->start_time)) }}-{{ date('H:i',strtotime($s->end_time)) }} @if($s->room)· {{$s->room}}@endif</p>
            @endforeach
        </div>@endif
    </div>
    @empty
    <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Belum terdaftar di kelas manapun.</div>
    @endforelse
</div>
@endsection
