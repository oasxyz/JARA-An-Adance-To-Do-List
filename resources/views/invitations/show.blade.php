@extends('layouts.app', ['title' => 'Undangan Kolaborasi List - Jara'])

@section('content')
<div class="max-w-xl mx-auto my-12 px-4">
    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-8 py-8 text-white text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/15 backdrop-blur-md mb-3 text-3xl">
                ✉️
            </div>
            <h1 class="text-2xl font-black tracking-tight">Undangan Kolaborasi List</h1>
            <p class="text-indigo-100 text-xs mt-1">Anda diundang untuk bergabung mengerjakan tugas bersama di Jara</p>
        </div>

        <!-- Detail Undangan -->
        <div class="p-8 space-y-6">
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-3">
                <div class="flex justify-between items-center text-xs text-slate-500 pb-3 border-b border-slate-200">
                    <span>Pengundang:</span>
                    <span class="font-bold text-slate-800 text-sm flex items-center">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs mr-2">
                            {{ strtoupper(substr($invitation->inviter->name, 0, 1)) }}
                        </span>
                        {{ $invitation->inviter->name }} ({{ $invitation->inviter->email }})
                    </span>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-500 pb-3 border-b border-slate-200">
                    <span>Nama List Tugas:</span>
                    <span class="font-black text-indigo-700 text-base">{{ $invitation->list->name }}</span>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-500">
                    <span>Status Undangan:</span>
                    @if($invitation->status === 'pending')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            ⏳ Menunggu Keputusan
                        </span>
                    @elseif($invitation->status === 'accepted')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            ✅ Sudah Diterima
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            ❌ Sudah Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Pesan / Informasi Hak Akses Anggota (SRS FR-09) -->
            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 text-xs text-indigo-900 space-y-1.5">
                <div class="font-bold flex items-center">
                    <span class="mr-1.5">👥</span> Hak Anda sebagai Anggota List:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-indigo-800/90 pl-1">
                    <li>Dapat membuat, mengedit, dan menyelesaikan tugas bersama.</li>
                    <li>Dapat ditugaskan (assigned) pada tugas tertentu.</li>
                    <li>Tidak dapat menghapus list atau mengundang anggota baru (khusus pemilik).</li>
                </ul>
            </div>

            <!-- Tombol Aksi Terima / Tolak (FR-08) -->
            @if($invitation->status === 'pending')
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <!-- Form Tolak Undangan -->
                    <form action="{{ route('invitations.reject', $invitation->token) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-xl border-2 border-rose-200 hover:border-rose-300 hover:bg-rose-50 text-rose-700 font-bold text-sm transition text-center shadow-xs">
                            ✕ Tolak Undangan
                        </button>
                    </form>

                    <!-- Form Terima Undangan -->
                    <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition text-center shadow-md shadow-indigo-200">
                            ✓ Terima Undangan
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center pt-2">
                    <a href="{{ route('lists.show', $invitation->list_id) }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition shadow-sm">
                        Buka List Ini &rarr;
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
