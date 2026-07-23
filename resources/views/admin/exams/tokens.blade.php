@extends('layouts.admin')
@section('title', 'Token Peserta')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Token Peserta: {{ $exam->title }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $participants->count() }} peserta terdaftar</p>
    </div>
    <a href="{{ route('admin.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">← Kembali</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Nama Peserta</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Token</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $p)
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-3 text-gray-400">{{ $loop->iteration }}</td>
                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $p->user->name }}</td>
                <td class="px-6 py-3 text-gray-500">{{ $p->user->email }}</td>
                <td class="px-6 py-3">
                    <code class="text-sm font-bold tracking-wider {{ $p->used_at ? 'text-gray-400 line-through' : 'text-indigo-600 dark:text-indigo-400' }}">{{ $p->token }}</code>
                </td>
                <td class="px-6 py-3">
                    @if($p->used_at)
                        <span class="text-xs text-gray-400">Terpakai {{ $p->used_at->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="text-xs font-medium text-green-600 dark:text-green-400">🔒 Belum dipakai</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-right">
                    <form action="{{ route('admin.exams.tokens.regenerate', [$exam, $p->id]) }}" method="POST" onsubmit="return confirm('Regenerate token untuk {{ $p->user->name }}?\nToken lama akan hangus dan tidak bisa dipakai lagi.')">
                        @csrf
                        <button class="text-xs font-medium text-amber-600 hover:text-amber-800 dark:text-amber-400">🔄 Regenerate</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
