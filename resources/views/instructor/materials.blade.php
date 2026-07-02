@extends('layouts.instructor')
@section('title', 'Materi')
@section('content')
<div class="flex items-center justify-between mb-6"><div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📚 Materi</h1><p class="text-gray-500 mt-1">Upload dan kelola modul, PDF, PPT, video, atau tautan pembelajaran.</p></div>
<a href="{{ route('admin.akademik.materi.create') }}" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ Tambah Materi</a></div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300"><tr><th class="px-6 py-3">Judul</th><th class="px-6 py-3">Kelas</th><th class="px-6 py-3">Urutan</th><th class="px-6 py-3">Aksi</th></tr></thead>
    <tbody>@forelse($materials as $m)<tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750"><td class="px-6 py-3 font-medium text-gray-900 dark:text-white text-sm">{{ $m->title }}</td><td class="px-6 py-3 text-gray-600 text-xs">{{ $m->schoolClass->name }}</td><td class="px-6 py-3 text-gray-600 text-xs">{{ $m->order }}</td><td class="px-6 py-3"><a href="{{ route('admin.akademik.materi.edit', $m) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">Edit</a></td></tr>@empty <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada materi.</td></tr>@endforelse</tbody></table>
    <div class="px-6 py-4">{{ $materials->links() }}</div>
</div>
@endsection
