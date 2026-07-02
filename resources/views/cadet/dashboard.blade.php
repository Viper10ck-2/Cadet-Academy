@extends('layouts.cadet')
@section('title','Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Halo, {{ auth()->user()->name }}! 👋</h1>
    <p class="text-gray-500 mt-1">{{ now()->locale('id')->translatedFormat('l, d F Y') }} · Tetap semangat belajar! 🚀</p>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @if(!$todayCheckIn)
    <a href="{{ route('attendance.index') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl font-medium text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-500/25">📸 Absen Sekarang</a>
    @else
    <div class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-xl font-medium text-sm">✅ Sudah Absen</div>
    @endif
    <a href="{{ route('cadet.cbt') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-amber-500 text-white rounded-xl font-medium text-sm hover:bg-amber-600 transition">💻 Try Out</a>
    <a href="{{ route('cadet.assignments') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-purple-500 text-white rounded-xl font-medium text-sm hover:bg-purple-600 transition">📝 Tugas</a>
    <a href="{{ route('cadet.materials') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-teal-500 text-white rounded-xl font-medium text-sm hover:bg-teal-600 transition">📖 Materi</a>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border text-center"><div class="text-2xl mb-1">📚</div><p class="text-2xl font-bold text-blue-600">{{ $myClasses->count() }}</p><p class="text-xs text-gray-500">Kelas Diikuti</p></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border text-center"><div class="text-2xl mb-1">📝</div><p class="text-2xl font-bold text-amber-600">{{ $pendingAssignments->count() }}</p><p class="text-xs text-gray-500">Tugas Pending</p></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border text-center"><div class="text-2xl mb-1">📊</div><p class="text-2xl font-bold text-green-600">{{ $progressPercent }}%</p><p class="text-xs text-gray-500">Progres</p></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border text-center"><div class="text-2xl mb-1">🏆</div><p class="text-2xl font-bold text-purple-600">{{ $recentGrades->avg('score') ?? 0 }}</p><p class="text-xs text-gray-500">Rata-rata Nilai</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Jadwal Hari Ini --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-3 border-b bg-blue-50 dark:bg-blue-900/20"><h3 class="font-semibold text-blue-800 dark:text-blue-300">📅 Jadwal Hari Ini ({{ ucfirst($todayName) }})</h3></div>
        <div class="divide-y">
            @forelse($todaySchedules as $s)
            <div class="flex items-center justify-between px-5 py-3"><div><p class="font-medium text-sm text-gray-900 dark:text-white">{{ $s->schoolClass->name }}</p><p class="text-xs text-gray-500">{{ $s->schoolClass->instructor->name ?? '-' }} @if($s->room)· {{ $s->room }}@endif</p></div><span class="text-sm font-semibold text-blue-600">{{ date('H:i',strtotime($s->start_time)) }} - {{ date('H:i',strtotime($s->end_time)) }}</span></div>
            @empty
            <p class="px-5 py-6 text-center text-gray-500 text-sm">Tidak ada jadwal hari ini. 🎉</p>
            @endforelse
        </div>
    </div>

    {{-- Tugas Pending --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-3 border-b bg-amber-50 dark:bg-amber-900/20 flex justify-between"><h3 class="font-semibold text-amber-800 dark:text-amber-300">📝 Tugas Belum Dikumpulkan</h3><a href="{{ route('cadet.assignments') }}" class="text-xs text-amber-600 hover:underline">Lihat Semua</a></div>
        <div class="divide-y">
            @forelse($pendingAssignments as $a)
            <div class="px-5 py-3"><p class="font-medium text-sm text-gray-900 dark:text-white">{{ $a->title }}</p><p class="text-xs text-gray-500">{{ $a->schoolClass->name }} · Deadline: <span class="text-red-500 font-medium">{{ $a->due_date->format('d M H:i') }}</span></p></div>
            @empty
            <p class="px-5 py-6 text-center text-gray-500 text-sm">Semua tugas sudah dikumpulkan! 🎉</p>
            @endforelse
        </div>
    </div>

    {{-- Try Out Aktif --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-3 border-b bg-purple-50 dark:bg-purple-900/20"><h3 class="font-semibold text-purple-800 dark:text-purple-300">💻 Try Out Aktif</h3></div>
        <div class="divide-y">
            @forelse($activeExams as $exam)
            <div class="flex items-center justify-between px-5 py-3"><div><p class="font-medium text-sm text-gray-900 dark:text-white">{{ $exam->title }}</p><p class="text-xs text-gray-500">{{ $exam->question_count }} soal · {{ $exam->duration_minutes }} menit</p></div>
            @if(in_array($exam->id,$myExamSessions))<span class="text-xs text-green-600 font-medium">✅ Selesai</span>@else<a href="{{ route('cbt.start',$exam) }}" onclick="event.preventDefault();document.getElementById('start-exam-{{$exam->id}}').submit()" class="px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700">Mulai</a><form id="start-exam-{{$exam->id}}" action="{{ route('cbt.start',$exam) }}" method="POST" class="hidden">@csrf</form>@endif</div>
            @empty
            <p class="px-5 py-6 text-center text-gray-500 text-sm">Tidak ada try out aktif.</p>
            @endforelse
        </div>
    </div>

    {{-- Nilai Terbaru --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-3 border-b bg-green-50 dark:bg-green-900/20"><h3 class="font-semibold text-green-800 dark:text-green-300">📊 Nilai Terbaru</h3></div>
        <div class="divide-y">
            @forelse($recentGrades as $g)
            <div class="flex items-center justify-between px-5 py-3"><p class="font-medium text-sm text-gray-900 dark:text-white">{{ $g->exam->title }}</p><span class="text-lg font-bold {{ $g->score >= $g->exam->passing_score ? 'text-green-600' : 'text-red-600' }}">{{ $g->score }}</span></div>
            @empty
            <p class="px-5 py-6 text-center text-gray-500 text-sm">Belum ada nilai.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
