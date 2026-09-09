{{-- 
    KOMPONEN PROGRESS TRACKER (FR-10)
    Props yang dibutuhkan:
    - $progress: array ['total', 'done', 'not_done', 'canceled', 'percentage']
    - $workload: collection dari getMembersWorkload()
--}}

<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
    
    <!-- Bagian 1: Header & Persentase Keseluruhan (FR-10) -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center">
                    <span class="mr-2">📊</span> Progress Tracker List (FR-10)
                </h3>
                <p class="text-xs text-slate-500">Persentase tugas selesai dan status penyelesaian tim</p>
            </div>
            
            <div class="flex items-baseline space-x-1">
                <span class="text-3xl font-black text-indigo-600">{{ $progress['percentage'] }}%</span>
                <span class="text-xs font-semibold text-slate-400">Selesai</span>
            </div>
        </div>

        <!-- Progress Bar Utama -->
        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
            <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-full rounded-full transition-all duration-700 ease-out"
                 style="width: {{ $progress['percentage'] }}%">
            </div>
        </div>

        <!-- Badges Breakdown Status (Done, Not Done, Canceled) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                <div class="text-[11px] text-slate-500 font-semibold">Total Tugas</div>
                <div class="text-base font-black text-slate-800">{{ $progress['total'] }}</div>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                <div class="text-[11px] text-emerald-700 font-semibold">✓ Selesai (Done)</div>
                <div class="text-base font-black text-emerald-800">{{ $progress['done'] }}</div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-center">
                <div class="text-[11px] text-amber-700 font-semibold">⏳ Belum Selesai</div>
                <div class="text-base font-black text-amber-800">{{ $progress['not_done'] }}</div>
            </div>
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center">
                <div class="text-[11px] text-rose-700 font-semibold">✕ Dibatalkan</div>
                <div class="text-base font-black text-rose-800">{{ $progress['canceled'] }}</div>
            </div>
        </div>
    </div>

    <hr class="border-slate-100">

    <!-- Bagian 2: Siapa Mengerjakan Apa (FR-10 Workload Rekap) -->
    <div>
        <div class="mb-4">
            <h4 class="text-xs font-bold text-slate-900 flex items-center uppercase tracking-wider">
                <span class="mr-2">👥</span> Siapa Mengerjakan Apa (Distribusi Tugas Anggota)
            </h4>
            <p class="text-[11px] text-slate-500">Pantau beban kerja dan progres tugas masing-masing anggota</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($workload as $item)
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    {{ $item['user']->name }}
                                    <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold {{ $item['role'] === 'Pemilik' ? 'bg-indigo-100 text-indigo-700' : ($item['role'] === 'Anggota' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600') }}">
                                        {{ $item['role'] }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400">{{ $item['user']->email }}</div>
                            </div>
                        </div>

                        <!-- Mini stats -->
                        <div class="text-right">
                            <span class="text-xs font-black text-indigo-600">{{ $item['percentage'] }}%</span>
                            <div class="text-[10px] text-slate-400">{{ $item['done'] }}/{{ $item['total'] }} Selesai</div>
                        </div>
                    </div>

                    <!-- Progress bar per user -->
                    <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500"
                             style="width: {{ $item['percentage'] }}%"></div>
                    </div>

                    <!-- List Tugas yang Ditugaskan ke User ini -->
                    <div class="pt-1">
                        <div class="text-[10px] font-semibold text-slate-500 mb-1">Tugas yang Ditugaskan:</div>
                        @if($item['tasks']->isEmpty())
                            <div class="text-[11px] text-slate-400 italic">Belum ada tugas yang ditugaskan.</div>
                        @else
                            <div class="space-y-1 max-h-32 overflow-y-auto pr-1">
                                @foreach($item['tasks'] as $t)
                                    <div class="flex items-center justify-between text-[11px] bg-white px-2 py-1 rounded border border-slate-200/70 shadow-2xs">
                                        <span class="truncate max-w-[180px] font-medium text-slate-700 {{ $t->status === 'done' ? 'line-through text-slate-400' : '' }}">
                                            {{ $t->title }}
                                        </span>
                                        @if($t->status === 'done')
                                            <span class="text-[9px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.2 rounded">✓ Done</span>
                                        @elseif($t->status === 'not done')
                                            <span class="text-[9px] bg-amber-100 text-amber-700 font-bold px-1.5 py-0.2 rounded">⏳ Not Done</span>
                                        @else
                                            <span class="text-[9px] bg-rose-100 text-rose-700 font-bold px-1.5 py-0.2 rounded">✕ Canceled</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
