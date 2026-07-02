@extends('layouts.admin')
@section('title', 'Users')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen User</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola semua pengguna Cadet Academy</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah User
    </a>
</div>

<!-- Filters -->
<form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau NIP/NIS..."
           class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
    <select name="role" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
        <option value="">Semua Role</option>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">Filter</button>
</form>

<!-- Table -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm text-left" id="usersTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">User</th>
                <th class="px-6 py-3">NIP/NIS</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Bergabung</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $user->nip_nis ?? '-' }}</td>
                <td class="px-6 py-3">{!! $user->role_badge !!}</td>
                <td class="px-6 py-3">
                    @if($user->email_verified_at)
                        <span class="text-green-600 dark:text-green-400 text-xs font-medium">✅ Verified</span>
                    @else
                        <span class="text-amber-600 dark:text-amber-400 text-xs font-medium">⚠️ Unverified</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                <td class="px-6 py-3 text-right">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data user.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
        {{ $users->links() }}
    </div>
</div>

@endsection
