<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Absensi') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6"
             x-data="{
                photo: null, latitude: null, longitude: null, locationText: 'Mendeteksi lokasi...', stream: null,
                async init() {
                    try { this.stream = await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:640,height:480}}); document.getElementById('camera').srcObject=this.stream; } catch(e){}
                    if(navigator.geolocation){ navigator.geolocation.getCurrentPosition(p=>{this.latitude=p.coords.latitude;this.longitude=p.coords.longitude;this.locationText=`${this.latitude.toFixed(6)}, ${this.longitude.toFixed(6)}`;},()=>{this.locationText='Gagal mendapatkan lokasi';},{enableHighAccuracy:true}); }
                },
                capture(){ const v=document.getElementById('camera'),c=document.getElementById('canvas'); c.width=v.videoWidth; c.height=v.videoHeight; c.getContext('2d').drawImage(v,0,0); this.photo=c.toDataURL('image/jpeg',0.8); alert('Foto berhasil diambil! ✅'); },
                async submitAttendance(){ if(!this.photo||!this.latitude){alert('Pastikan foto sudah diambil dan lokasi terdeteksi.');return} const type={{ $hasCheckedIn&&!$hasCheckedOut?"'check_out'":"'check_in'" }}; const fd=new FormData(); fd.append('type',type); fd.append('latitude',this.latitude); fd.append('longitude',this.longitude); fd.append('photo',this.photo); fd.append('_token','{{csrf_token()}}'); try{const r=await fetch('{{route("attendance.store")}}',{method:'POST',body:fd,headers:{'Accept':'application/json'}});const d=await r.json();if(d.success){alert(d.message);location.reload()}else{alert('Gagal: '+(d.message||'Terjadi kesalahan'))}}catch(e){alert('Gagal mengirim absensi.')} }
             }">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $hasCheckedIn && !$hasCheckedOut ? 'Check-Out' : 'Check-In' }} Hari Ini</h3>
                <div class="mb-4"><video id="camera" autoplay playsinline class="w-full rounded-xl bg-gray-900" style="max-height: 300px; object-fit: cover;"></video><canvas id="canvas" class="hidden"></canvas></div>
                <button @click="capture" class="w-full mb-4 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600">📸 Ambil Foto</button>
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-sm"><div class="flex items-center gap-2 mb-1"><span>📍</span><span class="text-gray-700 dark:text-gray-300 font-medium">Lokasi Anda:</span></div><p class="text-xs text-gray-500" x-text="locationText">Mendeteksi lokasi...</p></div>
                <div class="flex gap-4 mb-4">
                    <div class="flex-1 p-3 rounded-lg text-center {{ $hasCheckedIn ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700' }}"><p class="text-xs text-gray-500">Check-In</p><p class="font-bold {{ $hasCheckedIn ? 'text-green-600' : 'text-gray-400' }}">{{ $hasCheckedIn ? '✅ '.$todayAttendance->where('type','check_in')->first()->created_at->format('H:i') : '--:--' }}</p></div>
                    <div class="flex-1 p-3 rounded-lg text-center {{ $hasCheckedOut ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700' }}"><p class="text-xs text-gray-500">Check-Out</p><p class="font-bold {{ $hasCheckedOut ? 'text-green-600' : 'text-gray-400' }}">{{ $hasCheckedOut ? '✅ '.$todayAttendance->where('type','check_out')->first()->created_at->format('H:i') : '--:--' }}</p></div>
                </div>
                @if(!$hasCheckedIn || ($hasCheckedIn && !$hasCheckedOut))
                <button @click="submitAttendance" :disabled="!photo||!latitude" class="w-full px-4 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition">📋 {{ $hasCheckedIn ? 'Check-Out' : 'Check-In' }} Sekarang</button>
                @else
                <div class="w-full px-4 py-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-medium rounded-lg text-center text-sm">✅ Absensi hari ini lengkap!</div>
                @endif
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Absensi</h3>
                <div class="space-y-2">@foreach($history as $date=>$items)<div class="flex items-center justify-between p-2 text-sm border-b dark:border-gray-700"><span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span><div class="flex gap-2"><span class="text-xs px-2 py-0.5 rounded {{ $items->where('type','check_in')->count()?'bg-green-100 text-green-700':'bg-red-100 text-red-700' }}">In: {{ $items->where('type','check_in')->first()?->created_at->format('H:i')??'--:--' }}</span><span class="text-xs px-2 py-0.5 rounded {{ $items->where('type','check_out')->count()?'bg-green-100 text-green-700':'bg-red-100 text-red-700' }}">Out: {{ $items->where('type','check_out')->first()?->created_at->format('H:i')??'--:--' }}</span></div></div>@endforeach</div>
            </div>
        </div>
    </div>
</x-app-layout>
