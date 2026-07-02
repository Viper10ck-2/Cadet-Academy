@extends('layouts.cadet')
@section('title','Tugas')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Tugas</h1><p class="text-gray-500 mt-1">Kerjakan dan kumpulkan tugas sebelum deadline.</p></div>
<div class="space-y-4">
    @forelse($assignments as $a)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-5" x-data="{open:false}">
        <div class="flex items-start justify-between cursor-pointer" @click="open=!open">
            <div><h3 class="font-semibold text-gray-900 dark:text-white">{{ $a->title }}</h3><p class="text-xs text-gray-500 mt-0.5">{{ $a->schoolClass->name }} · Deadline: <span class="{{ $a->due_date->isPast() ? 'text-red-500' : 'text-amber-500' }} font-medium">{{ $a->due_date->format('d M Y H:i') }}</span> · Nilai Max: {{ $a->max_score }}</p></div>
            <div class="flex items-center gap-2">
                @php $mySub = $a->submissions->first(); @endphp
                @if($mySub)
                    @if($mySub->score!==null)<span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium">✅ {{ $mySub->score }}</span>
                    @else<span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">📤 Terkumpul</span>@endif
                @else<span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full">⏳ Pending</span>@endif
                <svg :class="open?'rotate-180':''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        <div x-show="open" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            @if($a->description)<p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $a->description }}</p>@endif
            @if(!$mySub || $mySub->score===null)
            <form method="POST" action="{{ route('cadet.assignments.submit',$a) }}" class="space-y-3">
                @csrf<textarea name="content" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white" placeholder="Tulis jawaban atau link pengumpulan..."></textarea>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">📤 Kumpulkan</button>
            </form>
            @else
            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm text-green-700 dark:text-green-400">✅ Tugas sudah dikumpulkan. @if($mySub->score!==null) Nilai: <b>{{ $mySub->score }}/{{ $a->max_score }}</b> @endif @if($mySub->feedback)<br>💬 {{ $mySub->feedback }}@endif</div>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center text-gray-500">Belum ada tugas.</div>
    @endforelse
</div>
<div class="mt-4">{{ $assignments->links() }}</div>
@endsection
