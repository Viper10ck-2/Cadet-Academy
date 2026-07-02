@extends('layouts.admin')
@section('title','Pantau Absensi')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📍 Pantau Absensi</h1>
    <p class="text-gray-500 mt-1">Pantau kehadiran seluruh cadet.</p>
</div>

<!-- Filter -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Kelas</label>
            <select name="class_id" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Filter</button>
        <a href="{{ route('admin.attendance') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-500 dark:text-gray-400">
            <tr>
                <th class="px-4 py-3 text-left">User</th>
                <th class="px-4 py-3 text-left">Tipe</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Jam</th>
                <th class="px-4 py-3 text-left">Lokasi</th>
                <th class="px-4 py-3 text-left">Foto</th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-gray-700">
            @forelse($attendances as $a)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ $a->user->avatar_url }}" class="w-7 h-7 rounded-full">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $a->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $a->user->nip_nis }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $a->type==='check_in'?'bg-green-100 text-green-700':'bg-red-100 text-red-700' }}">
                        {{ $a->type==='check_in'?'Masuk':'Pulang' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $a->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $a->created_at->format('H:i') }}</td>
                <td class="px-4 py-3 text-xs text-gray-500 max-w-[200px] truncate">{{ $a->latitude }}, {{ $a->longitude }}</td>
                <td class="px-4 py-3">
                    @if($a->photo_path)
                    <a href="{{ asset('storage/'.$a->photo_path) }}" target="_blank" class="text-indigo-600 text-xs hover:underline">Lihat</a>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada data absensi.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $attendances->links() }}</div>
</div>
@endsection
