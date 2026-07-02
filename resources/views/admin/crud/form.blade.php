@extends('layouts.admin')
@section('title', ($item ? 'Edit' : 'Tambah') . ' ' . $title)
@section('content')

<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $item ? 'Edit' : 'Tambah' }} {{ $title }}</h1>

    <form action="{{ $item ? route($routePrefix . '.update', $item->id) : route($routePrefix . '.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
        @csrf
        @if($item) @method('PUT') @endif

        @foreach($fillable as $field)
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ Str::title(str_replace('_', ' ', $field)) }}</label>
            @php $val = old($field, $item?->$field); @endphp
            @if(Str::contains($field, ['description', 'content', 'notes', 'address', 'answer', 'message']))
                <textarea name="{{ $field }}" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">{{ $val }}</textarea>
            @elseif(Str::startsWith($field, 'is_') || Str::startsWith($field, 'show_') || Str::startsWith($field, 'shuffle_'))
                <label class="flex items-center gap-2"><input type="checkbox" name="{{ $field }}" value="1" {{ $val ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Aktif</label>
            @elseif(Str::contains($field, ['date', 'time', 'at']))
                <input type="datetime-local" name="{{ $field }}" value="{{ $val instanceof \DateTime ? $val->format('Y-m-d\TH:i') : $val }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            @elseif(Str::contains($field, ['file', 'photo', 'image', 'avatar', 'path']))
                <input type="text" name="{{ $field }}" value="{{ $val }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            @else
                <input type="text" name="{{ $field }}" value="{{ $val }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            @endif
            @error($field) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        @endforeach

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route($routePrefix . '.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>

@endsection
