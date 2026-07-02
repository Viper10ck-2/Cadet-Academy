@extends('layouts.cadet')
@section('title','Notifikasi')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">🔔 Notifikasi</h1><p class="text-gray-500 mt-1">Informasi kelas, tugas, ujian, dan pengumuman.</p></div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
    @forelse($notifications as $n)
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-start gap-3 {{ is_null($n->read_at) ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
        <div class="flex-shrink-0 mt-1"><div class="w-2.5 h-2.5 {{ is_null($n->read_at) ? 'bg-blue-600' : 'bg-gray-300' }} rounded-full"></div></div>
        <div class="flex-1"><p class="text-sm text-gray-900 dark:text-white">{{ $n->data['message'] ?? 'Notifikasi' }}</p><p class="text-xs text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</p></div>
    </div>
    @empty
    <div class="px-6 py-10 text-center text-gray-500">Tidak ada notifikasi.</div>
    @endforelse
    <div class="px-6 py-4">{{ $notifications->links() }}</div>
</div>
@endsection
