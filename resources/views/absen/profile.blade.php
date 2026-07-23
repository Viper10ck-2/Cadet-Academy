@extends('layouts.absen')
@section('title','Profil')
@section('content')
<div class="flex-1 navy-gradient px-5 pt-8 overflow-y-auto">
    <div class="text-center mb-8">
        <img src="{{ $user->avatar_url }}" class="w-24 h-24 rounded-full mx-auto border-4 border-[#D4A853] shadow-lg mb-3">
        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
        <p class="text-gray-400 text-sm">{{ $user->nip_nis ?? 'N/A' }}</p>
        <p class="text-gray-400 text-xs">{{ $user->email }}</p>
    </div>

    <div class="bg-white/5 rounded-2xl border border-white/10 divide-y divide-white/10 mb-4">
        <div class="flex justify-between px-5 py-4 text-sm"><span class="text-gray-400">Phone</span><span class="text-white">{{ $user->phone ?? '—' }}</span></div>
        <div class="flex justify-between px-5 py-4 text-sm"><span class="text-gray-400">Gender</span><span class="text-white">{{ $user->gender === 'male' ? 'Laki-laki' : ($user->gender === 'female' ? 'Perempuan' : '—') }}</span></div>
        <div class="flex justify-between px-5 py-4 text-sm"><span class="text-gray-400">Role</span><span class="text-[#D4A853] font-medium">Cadet</span></div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <input type="hidden" name="absen" value="1">
        <button class="w-full py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 font-medium text-sm hover:bg-red-500/20">🚪 Keluar</button>
    </form>
</div>
@endsection
