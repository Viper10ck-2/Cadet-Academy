@extends('layouts.cadet')
@section('title','Diskusi')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">💬 Diskusi</h1><p class="text-gray-500 mt-1">Bertanya kepada tutor atau berdiskusi dengan teman sekelas.</p></div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-6 max-w-2xl">
    <form class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label><select class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">@foreach(auth()->user()->classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
    <div><textarea rows="3" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white" placeholder="Tulis pertanyaan atau diskusi..."></textarea></div>
    <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">💬 Kirim</button>
    </form>
</div>
<div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-6 text-center text-gray-500 text-sm">Belum ada diskusi.</div>
@endsection
