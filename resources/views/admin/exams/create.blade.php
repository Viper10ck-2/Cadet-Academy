@extends('layouts.admin')
@section('title', 'Buat Ujian')
@section('content')

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Buat Ujian Baru</h1>
    <form action="{{ route('admin.exams.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        @csrf

        {{-- Row 1: Judul + Tipe --}}
        <div class="grid grid-cols-3 gap-5">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Ujian *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Ujian *</label>
                <select name="type" id="examType" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="regular">Reguler</option>
                    <option value="tryout_skd" {{ old('type') === 'tryout_skd' ? 'selected' : '' }}>Tryout SKD</option>
                    <option value="mini_quiz" {{ old('type') === 'mini_quiz' ? 'selected' : '' }}>Mini Quiz</option>
                </select>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
        </div>

        {{-- Row 2: Durasi, KKM, Mulai, Selesai --}}
        <div class="grid grid-cols-4 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi (menit) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" required min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">KKM *</label>
                <input type="number" name="passing_score" value="{{ old('passing_score', 70) }}" required min="0" max="100"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Waktu Mulai *</label>
                <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Waktu Selesai *</label>
                <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        {{-- Komposisi Soal (Auto-generate dari Bank) --}}
        <div id="compositionSection" class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📋 Komposisi Soal (auto-generate dari Bank Soal)</p>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">TWK</label>
                    <input type="number" name="twk_count" value="{{ old('twk_count', 30) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-gray-400 mt-0.5">Bank: {{ \App\Models\Question::whereNull('exam_id')->where('category','TWK')->count() }} soal</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">TIU</label>
                    <input type="number" name="tiu_count" value="{{ old('tiu_count', 35) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-gray-400 mt-0.5">Bank: {{ \App\Models\Question::whereNull('exam_id')->where('category','TIU')->count() }} soal</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">TKP</label>
                    <input type="number" name="tkp_count" value="{{ old('tkp_count', 45) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-gray-400 mt-0.5">Bank: {{ \App\Models\Question::whereNull('exam_id')->where('category','TKP')->count() }} soal</p>
                </div>
            </div>
        </div>

        {{-- Peserta --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">👥 Peserta yang Boleh Ikut</label>
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700 max-h-60 overflow-y-auto">
                <label class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-200 dark:border-gray-700 cursor-pointer">
                    <input type="checkbox" id="selectAll" onclick="document.querySelectorAll('.cadet-check').forEach(c => c.checked = this.checked)"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Pilih Semua</span>
                </label>
                <div class="grid grid-cols-2 gap-1">
                    @foreach($cadets as $cadet)
                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white dark:hover:bg-gray-800 cursor-pointer transition">
                        <input type="checkbox" name="participant_ids[]" value="{{ $cadet->id }}" class="cadet-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               {{ in_array($cadet->id, old('participant_ids', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $cadet->name }}</span>
                    </label>
                    @endforeach
                </div>
                @if($cadets->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">Belum ada cadet terdaftar.</p>
                @endif
            </div>
        </div>

        {{-- Options --}}
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input type="checkbox" name="shuffle_questions" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Acak soal</label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input type="checkbox" name="show_result" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Tampilkan hasil</label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan & Generate Soal</button>
        </div>
    </form>
</div>

{{-- Auto-set durasi untuk Tryout SKD --}}
<script>
document.getElementById('examType').addEventListener('change', function() {
    const dur = document.querySelector('input[name="duration_minutes"]');
    const twk = document.querySelector('input[name="twk_count"]');
    const tiu = document.querySelector('input[name="tiu_count"]');
    const tkp = document.querySelector('input[name="tkp_count"]');
    if (this.value === 'tryout_skd') {
        if (dur) dur.value = 100;
        if (twk) twk.value = 30;
        if (tiu) tiu.value = 35;
        if (tkp) tkp.value = 45;
    }
});
</script>
@endsection
