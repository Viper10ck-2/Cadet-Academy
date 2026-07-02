@extends('layouts.admin')
@section('title', $location ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('content')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
    #map { height: 420px; border-radius: 12px; z-index: 1; }
    .leaflet-control-attribution { font-size: 10px; }
</style>
@endpush

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $location ? 'Edit' : 'Tambah' }} Lokasi Absensi</h1>

    <form action="{{ $location ? route('admin.lokasi.update', $location) : route('admin.lokasi.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        @csrf @if($location) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lokasi *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $location->name ?? '') }}" required
                   placeholder="Contoh: Gedung Utama, Kampus A, Ruang 101..."
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- 🗺️ Interactive Map --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">🗺️ Pilih Lokasi di Peta <span class="text-gray-400 font-normal">(klik atau geser marker)</span></label>
                <button type="button" onclick="detectMyLocation()" id="detectBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="detectText">Deteksi Lokasi Saya</span>
                </button>
            </div>
            {{-- Accuracy indicator --}}
            <div id="accuracyBar" class="hidden"></div>
            <div id="map" class="border border-gray-200 dark:border-gray-700 relative">
                <div id="mapLoading" class="absolute inset-0 z-[1000] bg-white/80 dark:bg-gray-900/80 hidden flex items-center justify-center rounded-xl">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <svg class="animate-spin w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Mendeteksi lokasi GPS...
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">🔵 <b>Klik</b> pada peta untuk memindahkan marker. <b>Geser</b> marker untuk posisi tepat. Lingkaran merah = radius toleransi. Tekan <b>"Deteksi Lokasi Saya"</b> untuk auto-detect via GPS.</p>
            <div id="gpsError" class="hidden mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg text-sm text-amber-800 dark:text-amber-200"></div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude *</label>
                <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $location->latitude ?? -6.2088) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                @error('latitude')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude *</label>
                <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $location->longitude ?? 106.8456) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                @error('longitude')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Radius Toleransi (meter) *</label>
            <div class="flex items-center gap-3">
                <input type="range" name="radius_meters" id="radiusSlider" value="{{ old('radius_meters', $location->radius_meters ?? 100) }}" min="10" max="5000" step="10"
                       class="flex-1 accent-indigo-600" oninput="updateRadius(this.value)">
                <span id="radiusVal" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 w-16 text-right">{{ old('radius_meters', $location->radius_meters ?? 100) }}m</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Jarak maksimal dari titik pusat lokasi. Rekomendasi: 50-200m untuk ruangan, 500-1000m untuk area kampus.</p>
            @error('radius_meters')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Aktifkan lokasi ini
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('admin.lokasi.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    const lat = parseFloat(document.getElementById('latitude').value) || -6.2088;
    const lng = parseFloat(document.getElementById('longitude').value) || 106.8456;
    const radius = parseInt(document.getElementById('radiusSlider').value) || 100;

    // Init map
    const map = L.map('map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
        maxZoom: 19
    }).addTo(map);

    // Radius circle
    const circle = L.circle([lat, lng], {
        radius: radius,
        color: '#ef4444',
        fillColor: '#ef4444',
        fillOpacity: 0.15,
        weight: 2,
        dashArray: '8 4'
    }).addTo(map);

    // Draggable marker
    const markerIcon = L.icon({
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
    });

    const marker = L.marker([lat, lng], { draggable: true, icon: markerIcon }).addTo(map)
        .bindPopup('<b>📍 Lokasi Absensi</b><br>Geser untuk atur posisi').openPopup();

    // Update inputs when marker moves
    function updateInputs(ll) {
        document.getElementById('latitude').value = ll.lat.toFixed(6);
        document.getElementById('longitude').value = ll.lng.toFixed(6);
        circle.setLatLng(ll);
    }

    marker.on('dragend', function(e) {
        updateInputs(e.target.getLatLng());
    });

    // Click on map to move marker
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng);
    });

    // Update circle when radius changes
    window.updateRadius = function(val) {
        document.getElementById('radiusVal').textContent = val + 'm';
        circle.setRadius(parseInt(val));
    };

    // Update marker when lat/lng inputs change manually
    document.getElementById('latitude').addEventListener('change', function() {
        const ll = L.latLng(parseFloat(this.value), parseFloat(document.getElementById('longitude').value));
        marker.setLatLng(ll);
        circle.setLatLng(ll);
        map.setView(ll, map.getZoom());
    });
    document.getElementById('longitude').addEventListener('change', function() {
        const ll = L.latLng(parseFloat(document.getElementById('latitude').value), parseFloat(this.value));
        marker.setLatLng(ll);
        circle.setLatLng(ll);
        map.setView(ll, map.getZoom());
    });

    // Current location marker (blue pulsing dot)
    let currentLocMarker = null;
    const currentLocIcon = L.divIcon({
        className: 'current-loc',
        html: '<div style="width:16px;height:16px;background:#3b82f6;border:3px solid white;border-radius:50%;box-shadow:0 0 0 3px rgba(59,130,246,0.3);animation:pulse-loc 2s infinite"></div>',
        iconSize: [16, 16],
        iconAnchor: [8, 8]
    });

    // 🎯 Detect My Location
    window.detectMyLocation = function() {
        const btn = document.getElementById('detectBtn');
        const text = document.getElementById('detectText');
        const loading = document.getElementById('mapLoading');

        if (!navigator.geolocation) {
            showGpsError('Geolocation tidak didukung di browser ini. Gunakan Chrome, Firefox, atau Edge terbaru.');
            return;
        }

        if (!navigator.onLine) {
            showGpsError('Tidak ada koneksi internet. GPS membutuhkan koneksi untuk memuat peta.');
            return;
        }

        btn.disabled = true;
        text.textContent = 'Mendeteksi...';
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const ll = L.latLng(pos.coords.latitude, pos.coords.longitude);
                const acc = Math.round(pos.coords.accuracy);
                marker.setLatLng(ll);
                updateInputs(ll);
                map.setView(ll, 18);

                if (currentLocMarker) map.removeLayer(currentLocMarker);
                currentLocMarker = L.marker(ll, { icon: currentLocIcon, zIndexOffset: 1000 }).addTo(map)
                    .bindPopup('<b>📍 Lokasi Anda Saat Ini</b><br>Akurasi: ±' + acc + 'm').openPopup();

                // Accuracy bar
                let accColor, accIcon, accMsg;
                if (acc <= 15) { accColor='bg-green-500'; accIcon='🟢'; accMsg='Sangat Akurat — seperti GPS HP'; }
                else if (acc <= 50) { accColor='bg-emerald-500'; accIcon='🟢'; accMsg='Akurat — cocok untuk area absensi'; }
                else if (acc <= 200) { accColor='bg-amber-500'; accIcon='🟡'; accMsg='Cukup — bisa dipakai, cek posisi di peta'; }
                else { accColor='bg-red-500'; accIcon='🔴'; accMsg='Kurang akurat — gunakan HP atau atur manual di peta'; }
                const bar = document.getElementById('accuracyBar');
                bar.className = 'mt-2 p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg';
                bar.innerHTML = '<div class="flex items-center gap-3"><span class="text-xl">'+accIcon+'</span><div class="flex-1"><div class="flex items-center justify-between mb-1"><span class="text-xs font-medium text-gray-700 dark:text-gray-300">Akurasi GPS</span><span class="text-xs font-bold '+(acc<=50?'text-green-600':'text-red-600')+'">±'+acc+'m</span></div><div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"><div class="'+accColor+' h-2 rounded-full transition-all" style="width:'+Math.min(100,acc/20*100)+'%"></div></div><p class="text-[11px] text-gray-500 mt-1">'+accMsg+'</p></div></div>';

                btn.disabled = false;
                text.textContent = '📍 Lokasi Terdeteksi!';
                loading.classList.add('hidden');
                loading.classList.remove('flex');
                document.getElementById('gpsError').classList.add('hidden');

                setTimeout(() => { text.textContent = 'Deteksi Lokasi Saya'; }, 2500);
            },
            function(err) {
                btn.disabled = false;
                text.textContent = 'Deteksi Lokasi Saya';
                loading.classList.add('hidden');
                loading.classList.remove('flex');

                let msg = '';
                switch(err.code) {
                    case 1: // PERMISSION_DENIED
                        msg = '⚠️ Izin lokasi belum diberikan.' +
                              '<br><small>Klik icon 🔒 di address bar → Allow location.<br>' +
                              'Atau: Settings → Privacy → Location → Allow.</small>' +
                              '<br><button onclick="detectMyLocation()" class="mt-2 px-3 py-1 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">🔄 Coba Lagi</button>';
                        break;
                    case 2: msg = '⚠️ Sinyal GPS tidak tersedia. Pastikan GPS/Location Service aktif di perangkat.'; break;
                    case 3: msg = '⚠️ Waktu deteksi habis. Coba lagi dengan koneksi yang lebih stabil.'; break;
                    default: msg = '⚠️ Gagal mendeteksi lokasi. Silakan coba lagi.';
                }
                showGpsError(msg);
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
        );
    };

    function showGpsError(msg) {
        const el = document.getElementById('gpsError');
        el.innerHTML = msg;
        el.classList.remove('hidden');
    }
</script>
<style>
    @keyframes pulse-loc {
        0%, 100% { box-shadow: 0 0 0 3px rgba(59,130,246,0.3); }
        50% { box-shadow: 0 0 0 8px rgba(59,130,246,0.1); }
    }
</style>
@endpush

