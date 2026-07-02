@extends('layouts.cadet')
@section('title','CBT / Try Out')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">💻 CBT / Try Out</h1><p class="text-gray-500 mt-1">Ikuti ujian online dan lihat hasilnya.</p></div>
<div class="space-y-3">
    @forelse($exams as $exam)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between"><div><h3 class="font-semibold text-gray-900 dark:text-white">{{ $exam->title }}</h3><p class="text-xs text-gray-500 mt-0.5">{{ $exam->questions_count }} soal · {{ $exam->duration_minutes }} menit · KKM {{ $exam->passing_score }}</p><p class="text-xs text-gray-500">🕐 {{ $exam->start_time->format('d M H:i') }} - {{ $exam->end_time->format('d M H:i') }}</p></div>
        @if(isset($mySessions[$exam->id]))
            @if($mySessions[$exam->id]->status==='finished'||$mySessions[$exam->id]->status==='timeout')
            <a href="{{ route('cbt.result',$mySessions[$exam->id]->id) }}" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Lihat Hasil</a>
            @else<a href="{{ route('cbt.take',$mySessions[$exam->id]->id) }}" class="px-4 py-2 bg-amber-600 text-white text-sm rounded-lg hover:bg-amber-700">Lanjutkan</a>@endif
        @else<form action="{{ route('cbt.start',$exam) }}" method="POST">@csrf<button class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">Mulai</button></form>@endif</div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Tidak ada try out yang sedang berlangsung.</div>
    @endforelse
</div>
<div class="mt-4 text-right"><a href="{{ route('cbt.history') }}" class="text-sm text-purple-600 hover:underline">📋 Riwayat Try Out →</a></div>
@endsection
