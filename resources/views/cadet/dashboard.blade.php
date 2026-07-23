@extends('layouts.cadet')
@section('title','Dashboard')
@section('content')

<div class="mb-6 animate-slide-right">
    <h1 class="text-2xl font-bold text-navy-900 dark:text-white tracking-tight">Halo, {{ auth()->user()->name }}! 👋</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ now()->locale('id')->translatedFormat('l, d F Y') }} · Tetap semangat belajar! 🚀</p>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 animate-fade-up">
    @if(!$todayCheckIn)
    <a href="{{ route('attendance.index') }}" class="flex items-center justify-center gap-2 px-4 py-3.5 bg-accent-500 text-white rounded-2xl font-semibold text-sm hover:bg-accent-600 transition-all shadow-lg shadow-accent-500/20 active:scale-95">📸 Absen Sekarang</a>
    @else
    <div class="flex items-center justify-center gap-2 px-4 py-3.5 bg-emerald-500 text-white rounded-2xl font-semibold text-sm shadow-lg shadow-emerald-500/20">✅ Sudah Absen</div>
    @endif
    <a href="{{ route('cadet.cbt') }}" class="flex items-center justify-center gap-2 px-4 py-3.5 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 rounded-2xl font-semibold text-sm hover:bg-amber-100 dark:hover:bg-amber-500/20 transition-all active:scale-95 border border-amber-200 dark:border-amber-500/20">💻 Try Out</a>
    <a href="{{ route('cadet.assignments') }}" class="flex items-center justify-center gap-2 px-4 py-3.5 bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 rounded-2xl font-semibold text-sm hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-all active:scale-95 border border-purple-200 dark:border-purple-500/20">📝 Tugas</a>
    <a href="{{ route('cadet.materials') }}" class="flex items-center justify-center gap-2 px-4 py-3.5 bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 rounded-2xl font-semibold text-sm hover:bg-teal-100 dark:hover:bg-teal-500/20 transition-all active:scale-95 border border-teal-200 dark:border-teal-500/20">📖 Materi</a>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 animate-fade-up">
    <div class="stat-card"><div class="stat-icon">📚</div><p class="stat-value text-accent-600 dark:text-accent-400">{{ $myClasses->count() }}</p><p class="stat-label">Kelas Diikuti</p></div>
    <div class="stat-card"><div class="stat-icon">📝</div><p class="stat-value text-amber-600 dark:text-amber-400">{{ $pendingAssignments->count() }}</p><p class="stat-label">Tugas Pending</p></div>
    <div class="stat-card"><div class="stat-icon">📊</div><p class="stat-value text-emerald-600 dark:text-emerald-400">{{ $progressPercent }}%</p><p class="stat-label">Progres</p></div>
    <div class="stat-card"><div class="stat-icon">🏆</div><p class="stat-value text-purple-600 dark:text-purple-400">{{ $recentGrades->avg('score') ?? 0 }}</p><p class="stat-label">Rata-rata Nilai</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 animate-fade-in">
    {{-- Jadwal Hari Ini --}}
    <div class="card-static overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-50 dark:border-navy-700 bg-accent-50/50 dark:bg-accent-500/5"><h3 class="font-semibold text-sm text-accent-700 dark:text-accent-400">📅 Jadwal Hari Ini ({{ ucfirst($todayName) }})</h3></div>
        <div class="divide-y divide-gray-50 dark:divide-navy-700">
            @forelse($todaySchedules as $s)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 dark:hover:bg-navy-700/30 transition-colors"><div><p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $s->schoolClass->name }}</p><p class="text-xs text-gray-500 mt-0.5">{{ $s->schoolClass->instructor->name ?? '-' }} @if($s->room)· {{ $s->room }}@endif</p></div><span class="badge badge-accent">{{ date('H:i',strtotime($s->start_time)) }} - {{ date('H:i',strtotime($s->end_time)) }}</span></div>
            @empty
            <p class="px-5 py-8 text-center text-gray-400 text-sm">Tidak ada jadwal hari ini. 🎉</p>
            @endforelse
        </div>
    </div>

    {{-- Tugas Pending --}}
    <div class="card-static overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-50 dark:border-navy-700 bg-amber-50/50 dark:bg-amber-500/5 flex justify-between items-center"><h3 class="font-semibold text-sm text-amber-700 dark:text-amber-400">📝 Tugas Belum Dikumpulkan</h3><a href="{{ route('cadet.assignments') }}" class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 font-medium">Lihat Semua →</a></div>
        <div class="divide-y divide-gray-50 dark:divide-navy-700">
            @forelse($pendingAssignments as $a)
            <div class="px-5 py-3.5 hover:bg-gray-50/50 dark:hover:bg-navy-700/30 transition-colors"><p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $a->title }}</p><p class="text-xs text-gray-500 mt-0.5">{{ $a->schoolClass->name }} · Deadline: <span class="text-red-500 font-semibold">{{ $a->due_date->format('d M H:i') }}</span></p></div>
            @empty
            <p class="px-5 py-8 text-center text-gray-400 text-sm">Semua tugas sudah dikumpulkan! 🎉</p>
            @endforelse
        </div>
    </div>

    {{-- Try Out Aktif --}}
    <div class="card-static overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-50 dark:border-navy-700 bg-purple-50/50 dark:bg-purple-500/5"><h3 class="font-semibold text-sm text-purple-700 dark:text-purple-400">💻 Try Out Aktif</h3></div>
        <div class="divide-y divide-gray-50 dark:divide-navy-700">
            @forelse($activeExams as $exam)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 dark:hover:bg-navy-700/30 transition-colors">
                <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $exam->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $exam->questions->count() }} soal · {{ $exam->duration_minutes }} menit</p>
                </div>
                @if(in_array($exam->id, $myExamSessions))
                @php $sess = \App\Models\ExamSession::where('user_id', auth()->id())->where('exam_id', $exam->id)->first(); @endphp
                @if($sess && $sess->status === 'finished')
                <span class="badge badge-green">✅ {{ $sess->score }}</span>
                @elseif($sess && $sess->status === 'in_progress')
                <a href="{{ route('cbt.take', $sess->id) }}" class="btn-primary" style="font-size:.75rem;padding:.375rem .75rem">Lanjutkan</a>
                @else
                <button onclick="startExam({{ $exam->id }}, '{{ $exam->title }}')" class="btn-primary" style="font-size:.75rem;padding:.375rem .75rem">Mulai</button>
                @endif
                @else
                <button onclick="startExam({{ $exam->id }}, '{{ $exam->title }}')" class="btn-primary" style="font-size:.75rem;padding:.375rem .75rem">Mulai</button>
                @endif
            </div>
            @empty
            <p class="px-5 py-8 text-center text-gray-400 text-sm">Tidak ada try out aktif.</p>
            @endforelse
        </div>
    </div>

    {{-- Nilai Terbaru --}}
    <div class="card-static overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-50 dark:border-navy-700 bg-emerald-50/50 dark:bg-emerald-500/5"><h3 class="font-semibold text-sm text-emerald-700 dark:text-emerald-400">📊 Nilai Terbaru</h3></div>
        <div class="divide-y divide-gray-50 dark:divide-navy-700">
            @forelse($recentGrades as $grade)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 dark:hover:bg-navy-700/30 transition-colors">
                <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $grade->exam?->title ?? 'Ujian #'.$grade->exam_id }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $grade->created_at->format('d M Y') }}</p>
                </div>
                <span class="text-lg font-bold {{ $grade->score >= 70 ? 'text-emerald-600' : 'text-red-500' }}">{{ $grade->score }}</span>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada nilai.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function startExam(examId, examTitle) {
    const token = prompt('🔑 Masukkan Token untuk "' + examTitle + '":\n\n(Token bisa dilihat di admin atau diberikan oleh pengajar)');
    if (!token) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/cbt/start/' + examId;
    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="token" value="' + token + '">';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
