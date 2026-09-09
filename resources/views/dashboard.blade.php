@extends('layouts.app')

@section('title', 'Dashboard - JARA')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-600 mb-6">Anda berhasil masuk sebagai <span class="font-semibold text-indigo-600">{{ strtoupper(auth()->user()->role) }}</span>.</p>

    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
        <h3 class="font-semibold text-indigo-900">Sprint Selanjutnya: Fitur Project & List Tasks</h3>
        <p class="text-sm text-indigo-700 mt-1">Sistem autentikasi dan otorisasi pengguna siap digunakan. Modul pengelolaan tugas pribadi dan tim dapat mulai dikembangkan di tahap berikutnya.</p>
    </div>
</div>
@endsection