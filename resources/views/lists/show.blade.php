@extends('layouts.app', ['title' => $list->name . ' - Jara'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header List -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                @if($isOwner)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-100 text-indigo-700">👑 Pemilik List</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-700">👥 Anggota List</span>
                @endif
                <span class="text-xs text-slate-400">&bull; Dibuat oleh {{ $list->owner->name }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">{{ $list->name }}</h1>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition">
                &larr; Dashboard
            </a>
            @if($isOwner)
                <button onclick="document.getElementById('inviteSection').scrollIntoView({behavior: 'smooth'})"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    + Undang Anggota
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Banner Ketika Ada Link Undangan Baru Digenerate -->
    @if(session('generated_invite_url'))
    <div class="bg-indigo-50 border-2 border-indigo-300 rounded-2xl p-5 shadow-sm space-y-3">
        <div class="flex items-center space-x-2 text-indigo-900 font-bold text-sm">
            <span>🎉</span>
            <span>Link Undangan Kolaborasi Berhasil Dibuat!</span>
        </div>
        <p class="text-xs text-indigo-700">Bagikan tautan berikut ke calon anggota tim Anda agar mereka dapat bergabung ke list ini:</p>
        
        <div class="flex items-center space-x-2">
            <input type="text" id="shareableLinkInput" readonly value="{{ session('generated_invite_url') }}"
                   class="flex-1 bg-white border border-indigo-200 px-3.5 py-2 rounded-xl text-xs font-mono text-slate-700 select-all">
            <button onclick="copyInviteLink()" 
                    id="copyBtn"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                📋 Salin Link
            </button>
        </div>
        @if(session('invited_email'))
            <div class="text-[11px] text-indigo-600 font-medium">
                Target Email: <strong>{{ session('invited_email') }}</strong> (Sistem juga mengirimkan undangan ke email tersebut).
            </div>
        @endif
    </div>
    @endif

    <!-- FITUR 1: SISTEM UNDANG ANGGOTA & DAFTAR ANGGOTA (FR-07) -->
    <div id="inviteSection" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <span class="mr-2">✉️</span> Sistem Undangan & Anggota Tim (FR-07 & FR-08)
                </h2>
                <p class="text-xs text-slate-500">Kelola anggota kolaborasi dalam list ini</p>
            </div>
            
            <div class="text-xs font-medium text-slate-500">
                Total Partisipan: <span class="font-bold text-slate-800">{{ $list->members->count() + 1 }} Orang</span>
            </div>
        </div>

        @if($isOwner)
        <!-- Form Buat Undangan (Khusus Pemilik List) -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Undang Anggota Baru (Email / Share Link):</h3>
            <form action="{{ route('invitations.store', $list->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <div class="flex-1">
                    <input type="email" name="invited_email" placeholder="Masukkan email rekan tim (opsional, misal: joko@example.com)" 
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <button type="submit" 
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition whitespace-nowrap">
                    Generate Link / Kirim Undangan &rarr;
                </button>
            </form>
            <p class="text-[11px] text-slate-400 mt-2">* Kosongkan email jika ingin membuat link umum, atau isi email untuk undangan spesifik.</p>
        </div>
        @else
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-500">
            ℹ️ Anda adalah <strong>Anggota</strong>. Sesuai hak akses (SRS Non-Functional Requirements), hanya <strong>Pemilik List</strong> yang memiliki hak mengundang anggota baru.
        </div>
        @endif

        <!-- Daftar Anggota yang Sudah Tergabung -->
        <div>
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Anggota yang Saat Ini Tergabung:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <!-- Pemilik -->
                <div class="bg-indigo-50/50 border border-indigo-200 p-3 rounded-xl flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                        {{ strtoupper(substr($list->owner->name, 0, 1)) }}
                    </span>
                    <div>
                        <div class="text-xs font-bold text-slate-800">{{ $list->owner->name }}</div>
                        <div class="text-[10px] text-indigo-700 font-semibold">👑 Pemilik (Owner)</div>
                    </div>
                </div>

                <!-- Anggota yang bergabung via invite -->
                @foreach($list->members as $member)
                    <div class="bg-white border border-slate-200 p-3 rounded-xl flex items-center space-x-3 shadow-2xs">
                        <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-800">{{ $member->name }}</div>
                            <div class="text-[10px] text-emerald-600 font-semibold">👥 Anggota (Joined)</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($isOwner && $invitations->count() > 0)
        <!-- Riwayat Undangan yang Dibuat (Khusus Pemilik) -->
        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Status Undangan Terkirim:</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-slate-600 font-semibold uppercase text-[10px]">
                        <tr>
                            <th class="p-2.5">Email Target</th>
                            <th class="p-2.5">Status</th>
                            <th class="p-2.5">Link Undangan</th>
                            <th class="p-2.5">Waktu Respon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invitations as $inv)
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-medium text-slate-800">{{ $inv->invited_email ?? 'Link Terbuka' }}</td>
                                <td class="p-2.5">
                                    @if($inv->status === 'pending')
                                        <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold text-[10px]">⏳ Pending</span>
                                    @elseif($inv->status === 'accepted')
                                        <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold text-[10px]">✅ Diterima</span>
                                    @else
                                        <span class="bg-rose-100 text-rose-800 px-2 py-0.5 rounded-full font-bold text-[10px]">❌ Ditolak</span>
                                    @endif
                                </td>
                                <td class="p-2.5 font-mono text-[11px] text-indigo-600">
                                    <a href="{{ route('invitations.show', $inv->token) }}" target="_blank" class="hover:underline">
                                        /invitations/{{ substr($inv->token, 0, 10) }}...
                                    </a>
                                </td>
                                <td class="p-2.5 text-slate-400 text-[11px]">
                                    {{ $inv->responded_at ? $inv->responded_at->diffForHumans() : 'Belum merespon' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

</div>

@push('scripts')
<script>
function copyInviteLink() {
    const linkInput = document.getElementById('shareableLinkInput');
    linkInput.select();
    navigator.clipboard.writeText(linkInput.value).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.innerText = '✅ Tersalin!';
        btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        btn.classList.add('bg-emerald-600');
        setTimeout(() => {
            btn.innerText = '📋 Salin Link';
            btn.classList.remove('bg-emerald-600');
            btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        }, 2000);
    });
}
</script>
@endpush
@endsection
