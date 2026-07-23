@extends('layouts.absen')
@section('title','Riwayat Absensi')
@section('content')
<div class="flex-1 navy-gradient px-5 pt-8 overflow-y-auto">
    <h2 class="text-xl font-bold text-white mb-6">📅 Riwayat Absensi</h2>
    <div class="space-y-3">
        @forelse($attendances as $date => $items)
        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
            <p class="text-[#D4A853] font-semibold text-sm mb-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M Y') }}</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-white/5 rounded-lg p-3 text-center">
                    <p class="text-gray-400">Masuk</p>
                    <p class="text-green-400 font-bold mt-1">{{ $items->where('type','check_in')->first()?->created_at->format('H:i') ?? '—' }}</p>
                </div>
                <div class="bg-white/5 rounded-lg p-3 text-center">
                    <p class="text-gray-400">Pulang</p>
                    <p class="text-red-400 font-bold mt-1">{{ $items->where('type','check_out')->first()?->created_at->format('H:i') ?? '—' }}</p>
                </div>
            </div>
            @php $ci = $items->where('type','check_in')->first(); @endphp
            @if($ci && $ci->photo_path)
            <img src="{{ asset('storage/'.$ci->photo_path) }}" class="w-16 h-16 rounded-lg mt-2 object-cover border border-white/10">
            @endif
        </div>
        @empty
        <div class="text-center py-10"><div class="text-5xl mb-3">📭</div><p class="text-gray-400 text-sm">Belum ada riwayat absensi.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $attendances instanceof \Illuminate\Pagination\LengthAwarePaginator ? $attendances->links() : '' }}</div>
</div>
@endsection
