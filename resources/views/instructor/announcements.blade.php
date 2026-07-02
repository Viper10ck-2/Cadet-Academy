@extends('layouts.instructor')
@section('title', 'Pengumuman & Pesan')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">💬 Pengumuman & Pesan</h1><p class="text-gray-500 mt-1">Mengirim informasi kepada siswa di kelas yang diampu.</p></div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 max-w-2xl">
    <form method="POST" class="space-y-4">
        @csrf
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas Tujuan</label>
        <select class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
            <option>Semua Kelas</option>
            @foreach(\App\Models\SchoolClass::where('instructor_id', auth()->id())->get() as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul</label><input type="text" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white" placeholder="Judul pengumuman..."></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan</label><textarea rows="4" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white" placeholder="Tulis pesan..."></textarea></div>
        <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">📨 Kirim Pengumuman</button>
    </form>
</div>

<div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 text-center text-gray-500 text-sm">
    Belum ada pengumuman yang dikirim.
</div>
@endsection
