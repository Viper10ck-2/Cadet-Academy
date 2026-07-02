@extends('layouts.instructor')
@section('title', 'Absensi')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📍 Absensi Siswa</h1><p class="text-gray-500 mt-1">Melihat kehadiran siswa beserta selfie dan lokasi GPS.</p></div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm"><thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300"><tr><th class="px-4 py-3">Siswa</th><th class="px-4 py-3">Tipe</th><th class="px-4 py-3">Lokasi GPS</th><th class="px-4 py-3">Foto</th><th class="px-4 py-3">Waktu</th></tr></thead>
    <tbody>@forelse($attendances as $a)<tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750"><td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-xs">{{ $a->user->name }}</td><td class="px-4 py-3 text-xs">{!! $a->type === 'check_in' ? '<span class="text-green-600">✅ Masuk</span>' : '<span class="text-red-600">🚪 Pulang</span>' !!}</td><td class="px-4 py-3 text-xs text-gray-500">{{ $a->latitude ? number_format($a->latitude,4).','.number_format($a->longitude,4) : '-' }}</td><td class="px-4 py-3">@if($a->photo_path)<span class="text-xs text-emerald-600">📸 Ada</span>@else<span class="text-xs text-gray-400">—</span>@endif</td><td class="px-4 py-3 text-xs text-gray-500">{{ $a->created_at->format('d/m/Y H:i') }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada data absensi.</td></tr>@endforelse</tbody></table>
    <div class="px-6 py-4">{{ $attendances->links() }}</div>
</div>
@endsection
