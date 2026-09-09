@extends('layouts.app', ['title' => 'Dashboard - Jara To-Do List'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Dashboard Jara</h1>
            <p class="text-xs text-slate-500 mt-1">Status login: <span class="font-bold text-indigo-600">{{ Auth::user()->name }}</span> ({{ Auth::user()->email }})</p>
        </div>

        <button onclick="document.getElementById('modalCreateList').classList.remove('hidden')" 
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
            + Buat List Baru
        </button>
    </div>

    <!-- NOTIFIKASI UNDANGAN MASUK (FR-08) -->
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
    <div class="bg-amber-50 border border-amber-300 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center space-x-2 text-amber-900 font-bold mb-3 text-sm">
            <span>📩</span>
            <span>Undangan Kolaborasi Masuk untuk Anda ({{ $pendingInvitations->count() }})</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($pendingInvitations as $inv)
                <div class="bg-white p-4 rounded-xl border border-amber-200 flex justify-between items-center shadow-xs">
                    <div>
                        <div class="font-bold text-slate-800 text-sm">{{ $inv->list->name }}</div>
                        <div class="text-[11px] text-slate-500">
                            Pengundang: <span class="font-semibold text-indigo-600">{{ $inv->inviter->name }}</span>
                        </div>
                    </div>
                    <a href="{{ route('invitations.show', $inv->token) }}" 
                       class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                        Lihat & Respon &rarr;
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 1. List Milik Saya (Pemilik) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <span class="mr-2">👑</span> List Milik Saya (Pemilik - Bisa Undang Anggota)
            </h2>
            <span class="text-xs bg-indigo-100 text-indigo-700 font-bold px-2 py-0.5 rounded-full">
                {{ $ownedLists->count() }} List
            </span>
        </div>

        @if($ownedLists->isEmpty())
            <p class="text-xs text-slate-400 italic">Belum ada list yang Anda buat.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($ownedLists as $list)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-indigo-50/20 transition flex justify-between items-center">
                        <div>
                            <div class="font-bold text-slate-800 text-sm">{{ $list->name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Dibuat {{ $list->created_at->diffForHumans() }}</div>
                        </div>
                        <a href="{{ route('lists.show', $list->id) }}" 
                           class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                            Buka & Undang &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2. List yang Diikuti (Anggota) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <span class="mr-2">👥</span> List yang Saya Ikuti (Anggota Kolaborasi)
            </h2>
            <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full">
                {{ $memberLists->count() }} List
            </span>
        </div>

        @if($memberLists->isEmpty())
            <p class="text-xs text-slate-400 italic">Anda belum bergabung dalam list manapun. Buka link undangan untuk bergabung.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($memberLists as $list)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-emerald-50/20 transition flex justify-between items-center">
                        <div>
                            <div class="font-bold text-slate-800 text-sm">{{ $list->name }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Pemilik: <span class="font-semibold text-slate-700">{{ $list->owner->name }}</span></div>
                        </div>
                        <a href="{{ route('lists.show', $list->id) }}" 
                           class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                            Masuk List &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- Modal Buat List -->
<div id="modalCreateList" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
        <h3 class="text-base font-bold text-slate-900 mb-3">Buat List Baru</h3>
        <form action="{{ route('lists.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama List</label>
                <input type="text" name="name" required placeholder="Contoh: Proyek Desain Web" 
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('modalCreateList').classList.add('hidden')" 
                        class="px-3.5 py-1.5 border border-slate-200 text-slate-600 rounded-lg text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold">Buat</button>
            </div>
        </form>
    </div>
</div>
@endsection
