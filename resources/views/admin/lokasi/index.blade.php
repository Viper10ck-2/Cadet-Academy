@extends('layouts.admin')
@section('title','Lokasi Absensi')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📍 Lokasi Absensi (Geofence)</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Atur area yang diizinkan untuk absensi. Cadet hanya bisa absen jika berada dalam radius lokasi yang aktif.</p>
    </div>
    <a href="{{ route('admin.lokasi.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Lokasi
    </a>
</div>

<div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl text-sm text-blue-700 dark:text-blue-300">
    💡 <strong>Cara kerja:</strong> Hanya <strong>satu lokasi aktif</strong> yang digunakan untuk validasi absensi. Pastikan koordinat GPS sesuai dengan lokasi fisik. Radius dalam meter menentukan toleransi jarak.
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($locations as $loc)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $loc->is_active ? 'border-green-300 dark:border-green-700 ring-2 ring-green-100 dark:ring-green-900/30' : 'border-gray-200 dark:border-gray-700' }} p-5 relative">
        @if($loc->is_active)
        <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-green-500 text-white text-[10px] font-bold rounded-full">AKTIF</span>
        @endif

        <h3 class="font-semibold text-gray-900 dark:text-white text-lg mb-3">{{ $loc->name }}</h3>

        <div class="space-y-2 text-sm mb-4">
            <div class="flex items-center gap-2"><span class="text-gray-400">📍</span><span class="text-gray-600 dark:text-gray-300 font-mono text-xs">{{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}</span></div>
            <div class="flex items-center gap-2"><span class="text-gray-400">🔵</span><span class="text-gray-600 dark:text-gray-300">Radius: <b>{{ $loc->radius_meters }}m</b></span></div>
            <div class="flex items-center gap-2"><span class="text-gray-400">📅</span><span class="text-gray-500 text-xs">Dibuat: {{ $loc->created_at->format('d M Y') }}</span></div>
        </div>

        {{-- Map Preview --}}
        <div class="mb-4 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div id="miniMap{{ $loc->id }}" class="w-full h-32"></div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.lokasi.edit', $loc) }}" class="flex-1 text-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">✏️ Edit</a>
            <form action="{{ route('admin.lokasi.toggle', $loc) }}" method="POST" class="flex-1">
                @csrf @method('PATCH')
                <button class="w-full px-3 py-2 text-xs font-medium rounded-lg transition {{ $loc->is_active ? 'text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-400 hover:bg-amber-100' : 'text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-100' }}">
                    {{ $loc->is_active ? '⏸ Nonaktifkan' : '▶️ Aktifkan' }}
                </button>
            </form>
            <form action="{{ route('admin.lokasi.destroy', $loc) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin hapus lokasi ini?')">
                @csrf @method('DELETE')
                <button class="w-full px-3 py-2 text-xs font-medium text-red-700 bg-red-50 dark:bg-red-900/30 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition">🗑️ Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-10 text-center">
        <div class="text-5xl mb-3">📍</div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Belum ada lokasi absensi</h3>
        <p class="text-gray-500 text-sm mb-4">Tambahkan lokasi untuk mulai memvalidasi absensi berdasarkan GPS.</p>
        <a href="{{ route('admin.lokasi.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">+ Tambah Lokasi Pertama</a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $locations->links() }}</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($locations as $loc)
    (function() {
        const el = document.getElementById('miniMap{{ $loc->id }}');
        if (!el) return;
        const map = L.map(el, { zoomControl: false, attributionControl: false, dragging: false, scrollWheelZoom: false, touchZoom: false, doubleClickZoom: false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        const ll = L.latLng({{ $loc->latitude }}, {{ $loc->longitude }});
        map.setView(ll, 16);
        L.circle(ll, { radius: {{ $loc->radius_meters }}, color: '#ef4444', fillColor: '#ef4444', fillOpacity: 0.15, weight: 2, dashArray: '6 3' }).addTo(map);
        L.marker(ll).addTo(map);
    })();
    @endforeach
});
</script>
@endpush
