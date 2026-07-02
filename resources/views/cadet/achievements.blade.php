@extends('layouts.cadet')
@section('title','Achievement')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">🏆 Achievement</h1><p class="text-gray-500 mt-1">Badge, sertifikat, dan pencapaian belajar Anda.</p></div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    @foreach([['📚','Pemula','Menyelesaikan 1 materi'],['📝','Rajin','Mengumpulkan 5 tugas'],['💻','Pejuang','Mengikuti 3 try out'],['📍','Disiplin','Absensi 100% 1 bulan'],['🏅','Bintang','Nilai rata-rata >80'],['🎓','Lulus','Lulus try out dengan nilai >KKM'],['🔥','Streak','Absen 7 hari berturut-turut'],['👑','Master','Menyelesaikan semua tugas']] as [$icon,$title,$desc])
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-5 text-center opacity-50">
        <div class="text-4xl mb-2">{{ $icon }}</div><h3 class="font-semibold text-sm text-gray-900 dark:text-white">{{ $title }}</h3><p class="text-xs text-gray-500 mt-1">{{ $desc }}</p>
    </div>
    @endforeach
</div>
<p class="text-center text-gray-400 text-sm mt-6">🔒 Selesaikan target untuk membuka achievement!</p>
@endsection
