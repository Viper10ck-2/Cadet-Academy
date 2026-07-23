@extends('layouts.admin')
@section('title', $title)
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data {{ strtolower($title) }}</p>
    </div>
    <a href="{{ route($routePrefix . '.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shrink-0">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span class="hidden sm:inline">Tambah</span>
    </a>
</div>

@if(session('status'))
    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)" class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg text-sm">{{ session('status') }}</div>
@endif

{{-- Mobile: Card View --}}
<div class="sm:hidden space-y-3">
    @forelse($items as $item)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0 space-y-1.5">
                @foreach($columns as $key => $label)
                    @php $val = data_get($item, $key); @endphp
                    <div class="flex items-baseline gap-2 text-sm">
                        <span class="text-gray-400 dark:text-gray-500 shrink-0">{{ $label }}:</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium truncate">
                            @if(is_bool($val))
                                <span class="text-xs px-2 py-0.5 rounded {{ $val ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">{{ $val ? 'Ya' : 'Tidak' }}</span>
                            @elseif($val instanceof \DateTime)
                                {{ $val->format('d/m/Y') }}
                            @else
                                {{ Str::limit((string)$val, 50) }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex gap-2 mt-3 pt-3 border-t border-gray-50 dark:border-gray-700">
            <a href="{{ route($routePrefix . '.edit', $item->id) }}" class="flex-1 text-center px-3 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-xs font-semibold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition">✏️ Edit</a>
            <form action="{{ route($routePrefix . '.destroy', $item->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin hapus?')">
                @csrf @method('DELETE')
                <button class="w-full px-3 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition">🗑️ Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-10 text-gray-400">Belum ada data.</div>
    @endforelse
    <div class="mt-4">{{ $items->links() }}</div>
</div>

{{-- Desktop: Table View --}}
<div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">#</th>
                @foreach($columns as $col)
                    <th class="px-6 py-3">{{ $col }}</th>
                @endforeach
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-3 text-gray-500">{{ $loop->iteration + $items->firstItem() - 1 }}</td>
                @foreach($columns as $key => $label)
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                        @php $val = data_get($item, $key); @endphp
                        @if(is_bool($val))
                            <span class="text-xs px-2 py-0.5 rounded {{ $val ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">{{ $val ? 'Ya' : 'Tidak' }}</span>
                        @elseif($val instanceof \DateTime)
                            {{ $val->format('d/m/Y') }}
                        @else
                            {{ Str::limit((string)$val, 40) }}
                        @endif
                    </td>
                @endforeach
                <td class="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                    <a href="{{ route($routePrefix . '.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-medium">✏️ Edit</a>
                    <form action="{{ route($routePrefix . '.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-medium">🗑️ Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="{{ count($columns) + 2 }}" class="px-6 py-10 text-center text-gray-500">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">{{ $items->links() }}</div>
</div>

@endsection
