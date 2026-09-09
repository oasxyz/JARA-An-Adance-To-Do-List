@extends('layouts.app')

@section('title', 'Tambah User - Admin Panel')

@section('content')
<div class="max-w-lg mx-auto bg-white p-8 border border-gray-200 rounded-xl shadow-md">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Tambah Akun Pengguna Baru</h2>
        <p class="text-xs text-gray-500">Buat akun pengguna dan tentukan hak aksesnya.</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Default</label>
            <input type="password" name="password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role / Hak Akses</label>
            <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User biasa</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
            </select>
            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow">
                Simpan Akun
            </button>
        </div>
    </form>
</div>
@endsection