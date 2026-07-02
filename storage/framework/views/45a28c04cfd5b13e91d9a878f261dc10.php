
<?php $__env->startSection('title','Login'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex-1 flex flex-col items-center justify-center p-6 navy-gradient min-h-screen">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🎓</div>
            <h1 class="text-2xl font-extrabold text-white">Cadet Academy</h1>
            <p class="text-gray-400 mt-2 text-sm">Sistem Absensi Digital</p>
        </div>
        <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div><input type="email" name="email" placeholder="Email" required class="w-full px-4 py-3 bg-white/10 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] outline-none text-sm"></div>
            <div><input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 bg-white/10 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] outline-none text-sm"></div>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-400 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <button type="submit" class="w-full py-3 btn-gold rounded-xl text-sm font-bold pulse">MASUK</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.absen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\6. PROJECT\Cadet Academy\resources\views/absen/login.blade.php ENDPATH**/ ?>