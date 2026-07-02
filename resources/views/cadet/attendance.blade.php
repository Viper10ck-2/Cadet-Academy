@extends('layouts.cadet')
@section('title','Absensi')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📍 Absensi</h1><p class="text-gray-500 mt-1">Riwayat kehadiran Anda.</p></div>

<div class="mb-6">@if(!$todayCheckIn)<a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition shadow-lg shadow-blue-500/25">📸 Absen Sekarang</a>@else<div class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-xl font-medium">✅ Sudah Absen Hari Ini</div>@endif</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm"><thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Check-In</th><th class="px-4 py-3">Check-Out</th><th class="px-4 py-3">Status</th></tr></thead>
    <tbody>@forelse($attendances->groupBy(fn($a)=>$a->created_at->format('Y-m-d')) as $date=>$items)<tr class="border-t dark:border-gray-700"><td class="px-4 py-3 font-medium text-xs">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td><td class="px-4 py-3 text-xs text-green-600">{{ $items->where('type','check_in')->first()?->created_at->format('H:i') ?? '—' }}</td><td class="px-4 py-3 text-xs text-red-600">{{ $items->where('type','check_out')->first()?->created_at->format('H:i') ?? '—' }}</td><td class="px-4 py-3">{!! $items->where('type','check_in')->count() ? '<span class="text-xs text-green-600">✅ Hadir</span>' : '<span class="text-xs text-red-500">❌ Alpha</span>' !!}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">Belum ada absensi.</td></tr>@endforelse</tbody></table>
    <div class="px-6 py-4">{{ $attendances->links() }}</div>
</div>
@endsection
