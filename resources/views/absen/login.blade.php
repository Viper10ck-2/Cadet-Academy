@extends('layouts.absen')
@section('title','Login')
@section('content')
<div class="flex-1 flex flex-col items-center justify-center p-6 navy-gradient min-h-screen">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🎓</div>
            <h1 class="text-2xl font-extrabold text-white">Cadet Academy</h1>
            <p class="text-gray-400 mt-2 text-sm">Sistem Absensi Digital</p>
        </div>
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div><input type="email" name="email" placeholder="Email" required class="w-full px-4 py-3 bg-white/10 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] outline-none text-sm"></div>
            <div><input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 bg-white/10 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] outline-none text-sm"></div>
            @error('email')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
            <button type="submit" class="w-full py-3 btn-gold rounded-xl text-sm font-bold pulse">MASUK</button>
        </form>
    </div>
</div>
@endsection
