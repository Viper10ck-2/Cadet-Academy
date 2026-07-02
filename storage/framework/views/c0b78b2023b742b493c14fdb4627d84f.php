
<?php $__env->startSection('title','Dashboard Absensi'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex-1 navy-gradient" x-data="absenApp()">
    
    <div class="px-5 pt-8 pb-6 text-center">
        <img src="<?php echo e($user->avatar_url); ?>" class="w-20 h-20 rounded-full mx-auto border-4 border-[#D4A853] shadow-lg mb-3">
        <h2 class="text-xl font-bold text-white"><?php echo e($user->name); ?></h2>
        <p class="text-gray-400 text-xs mt-0.5"><?php echo e($user->nip_nis ?? 'N/A'); ?></p>
        <?php $__currentLoopData = $myClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="inline-block mt-1 px-2 py-0.5 bg-[#D4A853]/20 text-[#D4A853] text-[10px] rounded-full"><?php echo e($c->name); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="mx-5 bg-white/5 backdrop-blur rounded-2xl p-5 border border-white/10 mb-4">
        <div class="grid grid-cols-2 gap-4 text-center">
            <div><p class="text-gray-400 text-[10px] uppercase tracking-wider">Hari</p><p class="text-white font-semibold text-sm mt-0.5"><?php echo e(now()->locale('id')->translatedFormat('l')); ?></p></div>
            <div><p class="text-gray-400 text-[10px] uppercase tracking-wider">Tanggal</p><p class="text-white font-semibold text-sm mt-0.5"><?php echo e(now()->format('d F Y')); ?></p></div>
            <div><p class="text-gray-400 text-[10px] uppercase tracking-wider">Jam</p><p class="text-white font-bold text-lg mt-0.5" x-text="clock" x-init="setInterval(()=>clock=new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'}),1000)"></p></div>
            <div>
                <p class="text-gray-400 text-[10px] uppercase tracking-wider">Status</p>
                <?php if($checkedIn): ?>
                <p class="text-green-400 font-bold text-sm mt-0.5">✅ Hadir</p>
                <p class="text-gray-500 text-[10px]"><?php echo e($checkedIn->created_at->format('H:i')); ?></p>
                <?php else: ?>
                <p class="text-red-400 font-bold text-sm mt-0.5">⚠️ Belum Absen</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="mx-5 mb-4">
        <?php if(!$activeSchedule): ?>
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 text-center">
            <p class="text-amber-400 text-sm font-medium">📅 Tidak ada jadwal aktif saat ini</p>
            <p class="text-amber-400/60 text-xs mt-1">Absensi hanya tersedia saat jam kelas berlangsung</p>
        </div>
        <?php elseif($checkedIn && !$checkedOut): ?>
        <button @click="doAbsen('check_out')" :disabled="loading" class="w-full py-4 bg-red-500/20 border border-red-500/40 rounded-2xl text-red-400 font-bold text-lg hover:bg-red-500/30 transition disabled:opacity-50">
            <span x-show="!loading">🚪 Absen Pulang</span>
            <span x-show="loading" class="flex items-center justify-center gap-2"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Memproses...</span>
        </button>
        <?php elseif(!$checkedIn): ?>
        <button @click="doAbsen('check_in')" :disabled="loading" class="w-full py-5 btn-gold rounded-2xl text-lg font-extrabold shadow-2xl shadow-[#D4A853]/30 pulse disabled:opacity-50">
            <span x-show="!loading">📸 ABSEN SEKARANG</span>
            <span x-show="loading" class="flex items-center justify-center gap-2"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Memproses...</span>
        </button>
        <?php else: ?>
        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-center">
            <p class="text-green-400 text-sm font-medium">✅ Absensi Hari Ini Lengkap</p>
        </div>
        <?php endif; ?>
    </div>

    
    <div x-show="showSuccess" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-[#0F172A]/95 backdrop-blur">
        <div class="w-full max-w-sm text-center" x-transition>
            <div class="text-7xl mb-4">✅</div>
            <h2 class="text-2xl font-extrabold text-[#D4A853] mb-2">Absensi Berhasil!</h2>
            <div class="bg-white/5 rounded-2xl p-5 border border-white/10 space-y-2 text-left mb-6">
                <div class="flex justify-between text-sm"><span class="text-gray-400">Jam</span><span class="text-white font-medium" x-text="result.waktu"></span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-400">Status</span><span class="text-green-400 font-medium" x-text="result.status"></span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-400">Lokasi</span><span class="text-white font-medium text-xs truncate max-w-[180px]" x-text="result.lokasi"></span></div>
            </div>
            <img :src="result.foto" class="w-32 h-32 rounded-2xl mx-auto border-2 border-[#D4A853] object-cover mb-4">
            <button @click="showSuccess=false;window.location.reload()" class="px-8 py-3 btn-gold rounded-xl font-bold">OK</button>
        </div>
    </div>

    
    <div x-show="error" x-cloak class="fixed top-4 inset-x-4 z-50 flex justify-center">
        <div class="bg-red-500/90 backdrop-blur text-white px-5 py-3 rounded-xl text-sm font-medium shadow-lg max-w-sm text-center" x-text="error" @click="error=''"></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init',()=>{Alpine.data('absenApp',()=>({
    loading:false,error:'',showSuccess:false,result:{},clock:new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}),
    async doAbsen(type){
        this.loading=true;this.error='';
        try{
            if(!navigator.geolocation){this.error='GPS tidak tersedia di perangkat ini.';this.loading=false;return}
            const pos=await new Promise((res,rej)=>navigator.geolocation.getCurrentPosition(res,rej,{enableHighAccuracy:true,timeout:10000,maximumAge:0}));
            const stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:640,height:480}});
            const video=document.createElement('video');video.srcObject=stream;await video.play();
            const canvas=document.createElement('canvas');canvas.width=video.videoWidth;canvas.height=video.videoHeight;
            canvas.getContext('2d').drawImage(video,0,0);
            const photo=canvas.toDataURL('image/jpeg',0.8);
            stream.getTracks().forEach(t=>t.stop());
            const res=await fetch('<?php echo e(route("absen.store")); ?>',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>','Accept':'application/json'},body:JSON.stringify({type,latitude:pos.coords.latitude,longitude:pos.coords.longitude,photo})});
            const data=await res.json();
            if(!res.ok){this.error=data.error||'Gagal absensi.';this.loading=false;return}
            this.result=data.data;this.showSuccess=true;
        }catch(e){this.error=e.message||'Gagal mengakses GPS/Kamera.'}
        this.loading=false;
    }
}))});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.absen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\6. PROJECT\Cadet Academy\resources\views/absen/dashboard.blade.php ENDPATH**/ ?>