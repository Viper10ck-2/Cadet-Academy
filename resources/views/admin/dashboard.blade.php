@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Selamat datang di panel administrasi Cadet Academy</p>
</div>

<!-- 8 Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- 📈 Total Siswa -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">📈 Total Siswa</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_siswa'] }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">Cadet terdaftar</p>
            </div>
            <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-xl">👨‍🎓</div>
        </div>
    </div>

    <!-- 👨‍🏫 Total Tutor -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">👨‍🏫 Total Tutor</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_tutor'] }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">Instruktur aktif</p>
            </div>
            <div class="w-11 h-11 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-xl">🧑‍🏫</div>
        </div>
    </div>

    <!-- 📚 Kelas Aktif -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">📚 Kelas Aktif</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['kelas_aktif'] }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">Ujian tersedia</p>
            </div>
            <div class="w-11 h-11 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-xl">📖</div>
        </div>
    </div>

    <!-- 💰 Pendapatan Bulan Ini -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">💰 Pendapatan Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pendapatan_bulan'] }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">Estimasi iuran</p>
            </div>
            <div class="w-11 h-11 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-xl">💵</div>
        </div>
    </div>

    <!-- 📅 Jadwal Hari Ini -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">📅 Jadwal Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['jadwal_hari_ini'] }}</p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">{{ now()->format('d M Y') }}</p>
            </div>
            <div class="w-11 h-11 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-xl">📆</div>
        </div>
    </div>

    <!-- ❌ Murid Alpha Hari Ini -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">❌ Murid Alpha Hari Ini</p>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['murid_alpha'] }}</p>
                <p class="text-xs text-red-500 dark:text-red-400 mt-1">Belum check-in</p>
            </div>
            <div class="w-11 h-11 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-xl">🚫</div>
        </div>
    </div>

    <!-- 📝 Try Out Berlangsung -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">📝 Try Out Berlangsung</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['tryout_berlangsung'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Sedang aktif</p>
            </div>
            <div class="w-11 h-11 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center text-xl">✍️</div>
        </div>
    </div>

    <!-- 🔔 Notifikasi -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition {{ $stats['notifikasi'] > 0 ? 'ring-2 ring-red-400 dark:ring-red-500' : '' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">🔔 Notifikasi</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['notifikasi'] }}</p>
                <p class="text-xs {{ $stats['notifikasi'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }} mt-1">
                    {{ $stats['notifikasi'] > 0 ? 'Belum dibaca' : 'Tidak ada' }}
                </p>
            </div>
            <div class="w-11 h-11 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center text-xl">🔔</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi User</h3>
        <canvas id="userChart" height="200"></canvas>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Penyelesaian Ujian (30 Hari)</h3>
        <canvas id="examChart" height="200"></canvas>
    </div>
</div>

<!-- Recent Sessions + Attendances -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Ujian Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Ujian</th>
                        <th class="px-4 py-3">Nilai</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSessions as $s)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white text-xs">{{ $s->user->name }}</td>
                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400 text-xs">{{ Str::limit($s->exam->title, 25) }}</td>
                        <td class="px-4 py-2.5 font-semibold text-xs {{ $s->is_passed ? 'text-green-600' : 'text-red-600' }}">{{ $s->score ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-xs">
                            @if($s->status === 'finished') <span class="text-green-600">✅</span>
                            @elseif($s->status === 'timeout') <span class="text-red-600">⏰</span>
                            @else <span class="text-blue-600">🔄</span> @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 text-xs">Belum ada aktivitas ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Absensi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $a)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white text-xs">{{ $a->user->name }}</td>
                        <td class="px-4 py-2.5 text-xs">
                            @if($a->type === 'check_in') <span class="text-green-600">✅ Masuk</span>
                            @else <span class="text-red-600">🚪 Pulang</span> @endif
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $a->latitude ? number_format($a->latitude, 4) . ', ' . number_format($a->longitude, 4) : '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $a->created_at->format('H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 text-xs">Belum ada absensi.</td></tr>
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
