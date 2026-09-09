@extends('layouts.app')

@section('title', 'Login - JARA')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 border border-gray-200 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Masuk ke JARA</h2>

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2">Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition shadow">
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Daftar sekarang</a>
    </p>
</div>
@endsection
