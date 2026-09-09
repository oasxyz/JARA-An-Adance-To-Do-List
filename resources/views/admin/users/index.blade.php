@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Akun Pengguna</h1>
            <p class="text-sm text-gray-500">Daftar seluruh akun terdaftar di sistem JARA.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow transition">
            + Tambah User Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Role</th>
                    <th class="px-6 py-3">Tanggal Terdaftar</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded transition">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada pengguna terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection