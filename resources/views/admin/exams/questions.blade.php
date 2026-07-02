@extends('layouts.admin')
@section('title', 'Soal - ' . $exam->title)
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Soal Ujian: {{ $exam->title }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $questions->count() }} soal | Total {{ $exam->total_points }} poin | KKM {{ $exam->passing_score }}</p>
    </div>
    <a href="{{ route('admin.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">← Kembali</a>
</div>

<!-- Add Question -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tambah Soal</h3>
    <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <textarea name="question_text" rows="2" required placeholder="Teks pertanyaan..."
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <select name="type" id="qtype" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                    <option value="multiple_choice">Pilihan Ganda</option>
                    <option value="essay">Essay</option>
                </select>
            </div>
            <div>
                <input type="number" name="points" value="1" min="1" placeholder="Poin"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>
            <div>
                <input type="text" name="correct_answer" required placeholder="Jawaban benar (A/B/C/D atau teks)"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>
        </div>
        <div id="optionsContainer" class="grid grid-cols-2 gap-3">
            @foreach(['A', 'B', 'C', 'D'] as $opt)
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Opsi {{ $opt }}</label>
                <input type="text" name="options[{{ $opt }}]" placeholder="Pilihan {{ $opt }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>
            @endforeach
        </div>
        <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Tambah Soal</button>
    </form>
</div>

<!-- Questions List -->
<div class="space-y-3">
    @foreach($questions as $q)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-medium px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">#{{ $loop->iteration }}</span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded {{ $q->type === 'multiple_choice' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">{{ $q->type === 'multiple_choice' ? 'PG' : 'Essay' }}</span>
                    <span class="text-xs text-gray-400">{{ $q->points }} poin</span>
                </div>
                <p class="text-gray-900 dark:text-white font-medium">{{ $q->question_text }}</p>
                @if($q->type === 'multiple_choice' && $q->options)
                <div class="mt-2 grid grid-cols-2 gap-1 text-sm text-gray-600 dark:text-gray-400">
                    @foreach($q->options as $key => $val)
                        <span class="{{ $key === $q->correct_answer ? 'text-green-600 dark:text-green-400 font-semibold' : '' }}">{{ $key }}. {{ $val }}</span>
                    @endforeach
                </div>
                @endif
                <p class="text-xs text-gray-400 mt-2">Jawaban: <span class="text-green-600 font-medium">{{ $q->correct_answer }}</span></p>
            </div>
            <form action="{{ route('admin.exams.questions.destroy', [$exam, $q]) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-sm">🗑️</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<script>
document.getElementById('qtype').addEventListener('change', function() {
    document.getElementById('optionsContainer').style.display = this.value === 'multiple_choice' ? 'grid' : 'none';
});
</script>
@endsection
