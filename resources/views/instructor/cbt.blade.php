@extends('layouts.instructor')
@section('title', 'CBT / Quiz')
@section('content')
<div class="flex items-center justify-between mb-6"><div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">💻 CBT / Quiz</h1><p class="text-gray-500 mt-1">Membuat dan mengelola soal, serta melihat hasil ujian siswa.</p></div>
<a href="{{ route('admin.exams.create') }}" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ Buat Ujian</a></div>

<div class="space-y-3">
    @forelse($exams as $exam)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $exam->title }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $exam->questions_count }} soal · {{ $exam->duration_minutes }} menit · KKM {{ $exam->passing_score }} · {{ $exam->sessions_count }} peserta</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.exams.questions', $exam) }}" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg hover:bg-emerald-100">Soal</a>
                <a href="{{ route('instructor.cbt.results', $exam) }}" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100">Hasil</a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Belum ada ujian. <a href="{{ route('admin.exams.create') }}" class="text-emerald-600 hover:underline">Buat ujian pertama →</a></div>
    @endforelse
</div>
@endsection
