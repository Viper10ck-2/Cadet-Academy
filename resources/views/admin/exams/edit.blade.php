@extends('layouts.admin')
@section('title', 'Edit Ujian')
@section('content')

<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Ujian: {{ $exam->title }}</h1>
    <form action="{{ route('admin.exams.update', $exam) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Ujian *</label>
            <input type="text" name="title" value="{{ old('title', $exam->title) }}" required
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $exam->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi (menit) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">KKM *</label>
                <input type="number" name="passing_score" value="{{ old('passing_score', $exam->passing_score) }}" required min="0" max="100"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Waktu Mulai *</label>
                <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Waktu Selesai *</label>
                <input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input type="checkbox" name="shuffle_questions" value="1" {{ $exam->shuffle_questions ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Acak soal</label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input type="checkbox" name="show_result" value="1" {{ $exam->show_result ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Tampilkan hasil</label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input type="checkbox" name="is_active" value="1" {{ $exam->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Aktif</label>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Update</button>
        </div>
    </form>
</div>

@endsection
