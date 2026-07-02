@extends('layouts.cadet')
@section('title','Materi')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📖 Materi Pembelajaran</h1><p class="text-gray-500 mt-1">Modul, video, PDF, dan materi dari kelas Anda.</p></div>
<div class="space-y-3">
    @forelse($materials as $m)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-5">
        <div class="flex items-start justify-between"><div><h3 class="font-semibold text-gray-900 dark:text-white">{{ $m->title }}</h3><p class="text-xs text-gray-500 mt-1">{{ $m->schoolClass->name }} · Urutan {{ $m->order }}</p></div>@if($m->file_path)<span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded">📎 File</span>@endif</div>
        @if($m->description)<p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $m->description }}</p>@endif
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Belum ada materi.</div>
    @endforelse
</div>
<div class="mt-4">{{ $materials->links() }}</div>
@endsection
