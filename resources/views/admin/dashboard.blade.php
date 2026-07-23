@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Selamat datang di panel administrasi Cadet Academy</p>
</div>

<!-- 8 Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Total Siswa -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Siswa</p>
                <p class="text-[28px] font-extrabold text-navy dark:text-white mt-1 leading-tight">{{ $stats['total_siswa'] }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Cadet terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-navy-50 dark:bg-navy-700/50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-navy-500 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Total Tutor -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Tutor</p>
                <p class="text-[28px] font-extrabold text-navy dark:text-white mt-1 leading-tight">{{ $stats['total_tutor'] }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Instruktur aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-navy-50 dark:bg-navy-700/50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-navy-500 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>

    <!-- Kelas Aktif -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kelas Aktif</p>
                <p class="text-[28px] font-extrabold text-navy dark:text-white mt-1 leading-tight">{{ $stats['kelas_aktif'] }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Program berjalan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gold-50 dark:bg-gold-900/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>
    </div>

    <!-- Pendapatan Bulan Ini -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Pendapatan Bulan Ini</p>
                <p class="text-[22px] font-extrabold text-navy dark:text-white mt-1 leading-tight">{{ $stats['pendapatan_bulan'] }}</p>
                <p class="text-[11px] text-emerald-500 font-medium mt-0.5">Estimasi iuran</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Jadwal Hari Ini</p>
                <p class="text-[28px] font-extrabold text-navy dark:text-white mt-1 leading-tight">{{ $stats['jadwal_hari_ini'] }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ now()->format('d M Y') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Murid Alpha Hari Ini -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 {{ $stats['murid_alpha'] > 0 ? 'border-l-4 border-l-red-400' : '' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Murid Alpha Hari Ini</p>
                <p class="text-[28px] font-extrabold {{ $stats['murid_alpha'] > 0 ? 'text-red-500' : 'text-navy dark:text-white' }} mt-1 leading-tight">{{ $stats['murid_alpha'] }}</p>
                <p class="text-[11px] {{ $stats['murid_alpha'] > 0 ? 'text-red-400' : 'text-gray-400 dark:text-gray-500' }} mt-0.5">Belum check-in</p>
            </div>
            <div class="w-12 h-12 rounded-2xl {{ $stats['murid_alpha'] > 0 ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:bg-navy-700/50' }} flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 {{ $stats['murid_alpha'] > 0 ? 'text-red-400' : 'text-gray-300 dark:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>
    </div>

    <!-- Try Out Berlangsung -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Try Out Berlangsung</p>
                <p class="text-[28px] font-extrabold text-navy dark:text-white mt-1 leading-tight">{{ $stats['tryout_berlangsung'] }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Sedang aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 {{ $stats['notifikasi'] > 0 ? 'border-l-4 border-l-gold-400 ring-1 ring-gold-200 dark:ring-gold-800' : '' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Notifikasi</p>
                <p class="text-[28px] font-extrabold {{ $stats['notifikasi'] > 0 ? 'text-gold-600 dark:text-gold-400' : 'text-navy dark:text-white' }} mt-1 leading-tight">{{ $stats['notifikasi'] }}</p>
                <p class="text-[11px] {{ $stats['notifikasi'] > 0 ? 'text-gold-500 font-medium' : 'text-gray-400 dark:text-gray-500' }} mt-0.5">
                    {{ $stats['notifikasi'] > 0 ? 'Belum dibaca' : 'Tidak ada' }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl {{ $stats['notifikasi'] > 0 ? 'bg-gold-50 dark:bg-gold-900/20' : 'bg-gray-50 dark:bg-navy-700/50' }} flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 {{ $stats['notifikasi'] > 0 ? 'text-gold-500' : 'text-gray-300 dark:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-navy dark:text-white mb-6">Distribusi User</h3>
        <canvas id="userChart" height="200"></canvas>
    </div>
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-navy dark:text-white mb-6">Penyelesaian Ujian (30 Hari)</h3>
        <canvas id="examChart" height="200"></canvas>
    </div>
</div>

<!-- Recent Sessions + Attendances -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-navy-700">
            <h3 class="text-base font-bold text-navy dark:text-white">Aktivitas Ujian Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-navy-700">
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Ujian</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Nilai</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-navy-700">
                    @forelse($recentSessions as $s)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-navy-700/50 transition-colors">
                        <td class="px-5 py-3 font-medium text-navy dark:text-white text-[13px]">{{ $s->user->name }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-[13px]">{{ Str::limit($s->exam->title, 25) }}</td>
                        <td class="px-5 py-3 font-semibold text-[13px] {{ $s->is_passed ? 'text-emerald-500' : 'text-red-400' }}">{{ $s->score ?? '-' }}</td>
                        <td class="px-5 py-3 text-[13px]">
                            @if($s->status === 'finished')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-medium bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400">Selesai</span>
                            @elseif($s->status === 'timeout')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-medium bg-red-50 text-red-500 dark:bg-red-900/20 dark:text-red-400">Timeout</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-medium bg-sky-50 text-sky-600 dark:bg-sky-900/20 dark:text-sky-400">Berjalan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400 text-[13px]">Belum ada aktivitas ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-navy-700">
            <h3 class="text-base font-bold text-navy dark:text-white">Absensi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-navy-700">
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Lokasi</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-navy-700">
                    @forelse($recentAttendances as $a)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-navy-700/50 transition-colors">
                        <td class="px-5 py-3 font-medium text-navy dark:text-white text-[13px]">{{ $a->user->name }}</td>
                        <td class="px-5 py-3 text-[13px]">
                            @if($a->type === 'check_in')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-medium bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400">Masuk</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-medium bg-red-50 text-red-500 dark:bg-red-900/20 dark:text-red-400">Pulang</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-[13px] font-mono">{{ $a->latitude ? number_format($a->latitude, 4) . ', ' . number_format($a->longitude, 4) : '-' }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-[13px]">{{ $a->created_at->format('H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400 text-[13px]">Belum ada absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="module">
import Chart from 'chart.js/auto';

// User Distribution Chart
const userCtx = document.getElementById('userChart');
if (userCtx) {
    new Chart(userCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($usersByRole)) !!},
            datasets: [{
                data: {!! json_encode(array_values($usersByRole)) !!},
                backgroundColor: ['#8b5cf6', '#3b82f6', '#22c55e'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}

// Exam Completions Chart
const examCtx = document.getElementById('examChart');
if (examCtx) {
    new Chart(examCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($examCompletions->pluck('date')) !!},
            datasets: [{
                label: 'Ujian Selesai',
                data: {!! json_encode($examCompletions->pluck('count')) !!},
                backgroundColor: '#6366f1',
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}
</script>
@endpush
