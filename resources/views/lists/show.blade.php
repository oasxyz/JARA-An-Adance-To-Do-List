@extends('layouts.app')

@section('title', 'Detail List #' . $listId . ' - Jara To-Do')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back to Dashboard -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-2">
                    List #{{ $listId }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900" id="list-title">Memuat List...</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                Dashboard
            </a>
        </div>
    </div>

    <!-- Tasks Area Placeholder / Container -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Tugas</h2>
                <p class="text-xs text-slate-500">Tugas yang terhubung dengan list ini</p>
            </div>
        </div>

        <div id="tasks-container" class="space-y-3">
            <div class="text-center py-12 text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p class="text-sm">Memuat tugas...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const listId = {{ $listId }};

    async function loadTasks() {
        try {
            const res = await fetch(`/api/lists/${listId}/tasks`, {
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();
            const container = document.getElementById('tasks-container');

            if (res.ok && json.data) {
                if (json.data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-10 text-slate-400">
                            <p class="text-sm">Belum ada tugas di dalam list ini.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = json.data.map(task => `
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/20 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full ${task.status === 'done' ? 'bg-emerald-500' : 'bg-amber-400'}"></span>
                            <div>
                                <h4 class="font-medium text-slate-900 ${task.status === 'done' ? 'line-through text-slate-400' : ''}">${task.title}</h4>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-400">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 font-medium text-slate-600">${task.priority}</span>
                                    ${task.deadline ? `<span>Deadline: ${new Date(task.deadline).toLocaleDateString()}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full ${task.status === 'done' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'}">
                            ${task.status}
                        </span>
                    </div>
                `).join('');
            }
        } catch (e) {
            console.error(e);
        }
    }

    loadTasks();
</script>
@endpush

