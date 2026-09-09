@extends('layouts.app', ['title' => 'Login - Jara'])

@section('content')
<div class="max-w-md mx-auto my-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Masuk ke Jara</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem Manajemen Tugas Multi-User & Kolaborasi</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow-sm">
                Masuk
            </button>
        </form>

        @if(isset($demoUsers) && $demoUsers->count() > 0)
        <!-- 1-Click Fast Login Khusus Demo Praktikum -->
        <div class="mt-8 pt-6 border-t border-slate-200">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider text-center mb-3">
                ⚡ Login Instan Akun Demo (Praktikum):
            </div>
            <div class="space-y-2">
                @foreach($demoUsers as $user)
                    <a href="{{ route('auth.switch', $user->id) }}" 
                       class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 transition group">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800 group-hover:text-indigo-600">{{ $user->name }}</div>
                                <div class="text-xs text-slate-400">{{ $user->email }}</div>
                            </div>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded font-medium group-hover:bg-indigo-600 group-hover:text-white transition">
                            Login &rarr;
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-6 text-center text-xs text-slate-500">
            Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Daftar sekarang</a>
        </div>
    </div>
</div>
@endsection
