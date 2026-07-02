@extends('layouts.instructor')
@section('title', 'Laporan')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📊 Laporan</h1><p class="text-gray-500 mt-1">Rekap kehadiran, nilai, dan perkembangan belajar siswa.</p></div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Per-Kelas --}}
    @foreach($classes as $class)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3 bg-emerald-50 dark:bg-emerald-900/30 border-b">
            <h3 class="font-semibold text-emerald-800 dark:text-emerald-300">{{ $class->name }}</h3>
            <p class="text-xs text-emerald-600/70">{{ $class->students_count }} siswa</p>
        </div>
        <div class="p-5 grid grid-cols-3 gap-3 text-center">
            <div><p class="text-2xl font-bold text-emerald-600">{{ $class->students_count }}</p><p class="text-xs text-gray-500">Siswa</p></div>
            <div><p class="text-2xl font-bold text-blue-600">{{ $class->materials_count }}</p><p class="text-xs text-gray-500">Materi</p></div>
            <div><p class="text-2xl font-bold text-orange-600">{{ $class->assignments_count }}</p><p class="text-xs text-gray-500">Tugas</p></div>
        </div>
    </div>
    @endforeach
</div>

@if($attendanceStats->count() > 0)
<div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-6">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">📈 Tren Kehadiran (30 Hari)</h3>
    <canvas id="attendanceChart" height="200"></canvas>
</div>
@endif
@endsection

@push('scripts')
@if($attendanceStats->count() > 0)
<script type="module">
import Chart from 'chart.js/auto';
new Chart(document.getElementById('attendanceChart'),{type:'line',data:{labels:{!! json_encode($attendanceStats->pluck('date')) !!},datasets:[{label:'Hadir',data:{!! json_encode($attendanceStats->pluck('count')) !!},borderColor:'#059669',backgroundColor:'#05966920'}]},options:{scales:{y:{beginAtZero:true}}}});
</script>
@endif
@endpush
