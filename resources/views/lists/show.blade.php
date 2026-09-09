@extends('layouts.app')

@section('title', $list->name . ' - Jara To-Do')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation / Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors group">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Header Section (Nama List Aktif) -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        List Aktif
                    </span>
                    <span id="header-task-count" class="text-xs text-slate-400 font-medium">
                        Memuat tugas...
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900" id="list-name-display">
                    {{ $list->name }}
                </h1>
            </div>
        </div>
    </div>

    <!-- Quick-Add Task Bar (FR-03, FR-04, FR-05) -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
        <form id="form-quick-add" onsubmit="submitQuickAddTask(event)" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Task Title Input -->
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    id="quick-task-title" 
                    name="title" 
                    required 
                    placeholder="Tambah tugas baru... (tekan Enter untuk simpan)" 
                    class="w-full pl-3.5 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none transition-all placeholder:text-slate-400"
                >
            </div>

            <div class="flex items-center gap-2">
                <!-- Priority Selector (FR-04) -->
                <div class="relative shrink-0">
                    <select 
                        id="quick-task-priority" 
                        name="priority"
                        class="px-3 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-xs font-semibold text-slate-700 bg-white outline-none cursor-pointer"
                        title="Tingkat Prioritas Tugas"
                    >
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <!-- Deadline Picker (FR-05) -->
                <div class="relative shrink-0">
                    <input 
                        type="date" 
                        id="quick-task-deadline" 
                        name="deadline" 
                        class="px-3 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-xs font-medium text-slate-700 bg-white outline-none"
                        title="Tenggat Waktu / Deadline"
                    >
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="btn-quick-add"
                    class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold tracking-wide shadow-sm transition-all duration-150 flex items-center justify-center gap-1.5 shrink-0 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks List Container (FR-03 s.d. FR-06) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Daftar Tugas</h2>
            <div class="flex items-center gap-2 text-xs">
                <button type="button" onclick="filterTasks('all')" id="tab-filter-all" class="px-2.5 py-1 rounded-lg font-semibold bg-indigo-50 text-indigo-700 transition-colors">Semua</button>
                <button type="button" onclick="filterTasks('not done')" id="tab-filter-active" class="px-2.5 py-1 rounded-lg font-medium text-slate-500 hover:text-slate-800 transition-colors">Aktif</button>
                <button type="button" onclick="filterTasks('done')" id="tab-filter-done" class="px-2.5 py-1 rounded-lg font-medium text-slate-500 hover:text-slate-800 transition-colors">Selesai</button>
            </div>
        </div>

        <!-- Skeleton Loading State -->
        <div id="tasks-loading" class="p-6 space-y-3">
            @for ($i = 0; $i < 3; $i++)
                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 animate-pulse">
                    <div class="flex items-center gap-3 w-3/4">
                        <div class="w-5 h-5 rounded-md bg-slate-200"></div>
                        <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                    </div>
                    <div class="h-4 bg-slate-100 rounded w-16"></div>
                </div>
            @endfor
        </div>

        <!-- Empty State -->
        <div id="tasks-empty" class="hidden text-center py-16 px-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum Ada Tugas</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                Gunakan baris input di atas untuk menambahkan tugas pertama Anda ke dalam list ini.
            </p>
        </div>

        <!-- Tasks Items List -->
        <div id="tasks-list" class="hidden divide-y divide-slate-100">
            <!-- Dynamic task items injected via JavaScript -->
        </div>
    </div>
</div>

<!-- ================= MODAL TASK DETAIL (FR-03 s.d. FR-06) ================= -->
<div id="modal-task-detail" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-0 bg-slate-900/40 backdrop-blur-xs transition-opacity">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all p-6">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <span id="modal-task-status-badge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                <span class="text-xs text-slate-400" id="modal-task-id-display"></span>
            </div>
            <button type="button" onclick="closeTaskDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Edit Form in Modal -->
        <form id="form-edit-task" onsubmit="submitUpdateTask(event)" class="mt-4 space-y-4">
            <input type="hidden" id="modal-task-id">

            <!-- Title -->
            <div>
                <label for="modal-task-title" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Judul Tugas</label>
                <input 
                    type="text" 
                    id="modal-task-title" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm font-medium text-slate-900 outline-none transition-all"
                >
            </div>

            <!-- Priority and Deadline Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="modal-task-priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Tingkat Prioritas (FR-04)</label>
                    <select 
                        id="modal-task-priority" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm font-medium text-slate-800 bg-white outline-none cursor-pointer"
                    >
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <label for="modal-task-deadline" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Deadline (FR-05)</label>
                    <input 
                        type="date" 
                        id="modal-task-deadline" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm font-medium text-slate-800 bg-white outline-none"
                    >
                </div>
            </div>

            <!-- Metadata Details -->
            <div class="bg-slate-50 rounded-xl p-3.5 text-xs text-slate-500 flex items-center justify-between">
                <span>Dibuat pada: <strong id="modal-task-created-at" class="text-slate-700"></strong></span>
                <button 
                    type="button" 
                    id="btn-toggle-modal-status" 
                    onclick="toggleStatusFromModal()" 
                    class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                >
                    Ubah Status
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    onclick="deleteTaskFromModal()" 
                    id="btn-delete-task"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Tugas
                </button>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        onclick="closeTaskDetailModal()" 
                        class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors"
                    >
                        Tutup
                    </button>
                    <button 
                        type="submit" 
                        id="btn-save-task-detail"
                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-all"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const currentListId = {{ $list->id }};
    let currentTasks = [];
    let currentFilter = 'all';
    let selectedTaskId = null;

    // Fetch all tasks for this list from GET /api/lists/{id}/tasks
    async function loadTasks() {
        const loading = document.getElementById('tasks-loading');
        const empty = document.getElementById('tasks-empty');
        const listContainer = document.getElementById('tasks-list');

        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        listContainer.classList.add('hidden');

        try {
            const res = await fetch(`/api/lists/${currentListId}/tasks`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await res.json();
            loading.classList.add('hidden');

            if (res.ok && data.data) {
                currentTasks = data.data;
                updateHeaderTaskCount();
                renderTasks();
            } else {
                window.showToast('Gagal memuat tugas.', 'error');
            }
        } catch (e) {
            loading.classList.add('hidden');
            console.error('Error fetching tasks:', e);
            window.showToast('Koneksi terputus saat mengambil data.', 'error');
        }
    }

    // Update header task count
    function updateHeaderTaskCount() {
        const countEl = document.getElementById('header-task-count');
        const total = currentTasks.length;
        const done = currentTasks.filter(t => t.status === 'done').length;
        countEl.textContent = `${total} Total Tugas (${done} Selesai)`;
    }

    // Filter tasks
    function filterTasks(filter) {
        currentFilter = filter;
        document.getElementById('tab-filter-all').className = filter === 'all' 
            ? 'px-2.5 py-1 rounded-lg font-semibold bg-indigo-50 text-indigo-700 transition-colors' 
            : 'px-2.5 py-1 rounded-lg font-medium text-slate-500 hover:text-slate-800 transition-colors';
        document.getElementById('tab-filter-active').className = filter === 'not done' 
            ? 'px-2.5 py-1 rounded-lg font-semibold bg-indigo-50 text-indigo-700 transition-colors' 
            : 'px-2.5 py-1 rounded-lg font-medium text-slate-500 hover:text-slate-800 transition-colors';
        document.getElementById('tab-filter-done').className = filter === 'done' 
            ? 'px-2.5 py-1 rounded-lg font-semibold bg-indigo-50 text-indigo-700 transition-colors' 
            : 'px-2.5 py-1 rounded-lg font-medium text-slate-500 hover:text-slate-800 transition-colors';

        renderTasks();
    }

    // Render tasks list
    function renderTasks() {
        const empty = document.getElementById('tasks-empty');
        const listContainer = document.getElementById('tasks-list');

        let filtered = currentTasks;
        if (currentFilter === 'not done') {
            filtered = currentTasks.filter(t => t.status !== 'done');
        } else if (currentFilter === 'done') {
            filtered = currentTasks.filter(t => t.status === 'done');
        }

        if (filtered.length === 0) {
            empty.classList.remove('hidden');
            listContainer.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        listContainer.classList.remove('hidden');

        listContainer.innerHTML = filtered.map(task => {
            const isDone = task.status === 'done';
            
            // Priority badge styling (FR-04)
            let priorityBadge = '';
            if (task.priority === 'High') {
                priorityBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">High</span>';
            } else if (task.priority === 'Low') {
                priorityBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">Low</span>';
            } else {
                priorityBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Medium</span>';
            }

            // Deadline indicator formatting (FR-05)
            let deadlineHtml = '';
            if (task.deadline) {
                const deadlineDate = new Date(task.deadline);
                const isOverdue = !isDone && deadlineDate < new Date().setHours(0,0,0,0);
                const dateString = deadlineDate.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                deadlineHtml = `
                    <span class="inline-flex items-center gap-1 text-xs ${isOverdue ? 'text-rose-600 font-semibold' : 'text-slate-400 font-medium'}">
                        <svg class="w-3.5 h-3.5 ${isOverdue ? 'text-rose-500' : 'text-slate-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>${isOverdue ? 'Terlewat: ' : 'Deadline: '}${dateString}</span>
                    </span>
                `;
            }

            return `
                <div 
                    class="flex items-center justify-between p-4 sm:px-6 hover:bg-slate-50/80 transition-colors cursor-pointer group select-none"
                    onclick="openTaskDetailModal(${task.id})"
                >
                    <!-- Left: Checkbox & Task Title -->
                    <div class="flex items-center gap-3.5 min-w-0 pr-4">
                        <!-- Checkbox (FR-06) with event.stopPropagation -->
                        <div class="relative flex items-center shrink-0" onclick="event.stopPropagation()">
                            <input 
                                type="checkbox" 
                                id="chk-task-${task.id}"
                                ${isDone ? 'checked' : ''} 
                                onchange="toggleTaskStatus(${task.id})"
                                class="w-5 h-5 rounded-lg border-2 border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-300 focus:ring-offset-0 transition-all cursor-pointer accent-indigo-600"
                            >
                        </div>

                        <!-- Title with strike-through when done -->
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold transition-all truncate ${isDone ? 'line-through text-slate-400' : 'text-slate-800 group-hover:text-indigo-600'}">
                                ${escapeHtml(task.title)}
                            </h3>
                            <div class="flex items-center gap-3 mt-1">
                                ${deadlineHtml}
                            </div>
                        </div>
                    </div>

                    <!-- Right: Priority Badge & View Details Hint -->
                    <div class="flex items-center gap-3 shrink-0">
                        ${priorityBadge}
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Toggle Task Status Instantly (FR-06)
    async function toggleTaskStatus(taskId) {
        const task = currentTasks.find(t => t.id === taskId);
        if (!task) return;

        // Optimistic UI update
        const previousStatus = task.status;
        task.status = (previousStatus === 'done') ? 'not done' : 'done';
        renderTasks();
        updateHeaderTaskCount();

        try {
            const res = await fetch(`/api/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({}) // Empty body triggers toggle
            });

            const data = await res.json();
            if (res.ok && data.data) {
                task.status = data.data.status;
                renderTasks();
                updateHeaderTaskCount();
                window.showToast(task.status === 'done' ? 'Tugas ditandai selesai!' : 'Tugas dibuka kembali.', 'success');
            } else {
                // Revert on error
                task.status = previousStatus;
                renderTasks();
                updateHeaderTaskCount();
                window.showToast('Gagal mengubah status tugas.', 'error');
            }
        } catch (e) {
            task.status = previousStatus;
            renderTasks();
            updateHeaderTaskCount();
            window.showToast('Terjadi kesalahan koneksi.', 'error');
        }
    }

    // Quick-Add Task (FR-03)
    async function submitQuickAddTask(event) {
        event.preventDefault();
        const titleInput = document.getElementById('quick-task-title');
        const prioritySelect = document.getElementById('quick-task-priority');
        const deadlineInput = document.getElementById('quick-task-deadline');
        const submitBtn = document.getElementById('btn-quick-add');

        const title = titleInput.value.trim();
        const priority = prioritySelect.value;
        const deadline = deadlineInput.value || null;

        if (!title) return;

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75');

        try {
            const res = await fetch(`/api/lists/${currentListId}/tasks`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title,
                    priority,
                    deadline
                })
            });

            const data = await res.json();
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');

            if (res.ok && data.data) {
                // Clear input
                titleInput.value = '';
                deadlineInput.value = '';
                prioritySelect.value = 'Medium';
                
                // Add new task to top of list
                currentTasks.unshift(data.data);
                renderTasks();
                updateHeaderTaskCount();
                window.showToast('Tugas baru berhasil ditambahkan!', 'success');
            } else {
                window.showToast(data.message || 'Gagal menambahkan tugas.', 'error');
            }
        } catch (e) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');
            window.showToast('Terjadi kesalahan sistem.', 'error');
        }
    }

    // ================= MODAL TASK DETAIL HANDLERS ================= //
    const taskDetailModal = document.getElementById('modal-task-detail');

    function openTaskDetailModal(taskId) {
        selectedTaskId = taskId;
        const task = currentTasks.find(t => t.id === taskId);
        if (!task) return;

        document.getElementById('modal-task-id').value = task.id;
        document.getElementById('modal-task-id-display').textContent = `#${task.id}`;
        document.getElementById('modal-task-title').value = task.title;
        document.getElementById('modal-task-priority').value = task.priority || 'Medium';

        // Format deadline for date input
        if (task.deadline) {
            const dateObj = new Date(task.deadline);
            const yyyy = dateObj.getFullYear();
            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
            const dd = String(dateObj.getDate()).padStart(2, '0');
            document.getElementById('modal-task-deadline').value = `${yyyy}-${mm}-${dd}`;
        } else {
            document.getElementById('modal-task-deadline').value = '';
        }

        // Status badge in modal
        const badge = document.getElementById('modal-task-status-badge');
        if (task.status === 'done') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800';
            badge.textContent = 'Selesai (Done)';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700';
            badge.textContent = 'Belum Selesai';
        }

        // Created date
        const createdDate = task.created_at ? new Date(task.created_at).toLocaleString('id-ID') : '-';
        document.getElementById('modal-task-created-at').textContent = createdDate;

        taskDetailModal.classList.remove('hidden');
        taskDetailModal.classList.add('flex');
    }

    function closeTaskDetailModal() {
        taskDetailModal.classList.add('hidden');
        taskDetailModal.classList.remove('flex');
        selectedTaskId = null;
    }

    // Update Task Details (PUT /api/tasks/{id})
    async function submitUpdateTask(event) {
        event.preventDefault();
        if (!selectedTaskId) return;

        const title = document.getElementById('modal-task-title').value.trim();
        const priority = document.getElementById('modal-task-priority').value;
        const deadline = document.getElementById('modal-task-deadline').value || null;
        const saveBtn = document.getElementById('btn-save-task-detail');

        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-75');

        try {
            const res = await fetch(`/api/tasks/${selectedTaskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title,
                    priority,
                    deadline
                })
            });

            const data = await res.json();
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-75');

            if (res.ok && data.data) {
                const index = currentTasks.findIndex(t => t.id === selectedTaskId);
                if (index !== -1) {
                    currentTasks[index] = data.data;
                }
                renderTasks();
                closeTaskDetailModal();
                window.showToast('Detail tugas berhasil diperbarui!', 'success');
            } else {
                window.showToast(data.message || 'Gagal menyimpan perubahan.', 'error');
            }
        } catch (e) {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-75');
            window.showToast('Terjadi kesalahan koneksi.', 'error');
        }
    }

    // Toggle Status directly from Modal
    async function toggleStatusFromModal() {
        if (!selectedTaskId) return;
        await toggleTaskStatus(selectedTaskId);
        // Refresh modal view
        openTaskDetailModal(selectedTaskId);
    }

    // Delete Task from Modal
    async function deleteTaskFromModal() {
        if (!selectedTaskId) return;
        if (!confirm('Apakah Anda yakin ingin menghapus tugas ini?')) return;

        const deleteBtn = document.getElementById('btn-delete-task');
        deleteBtn.disabled = true;

        try {
            const res = await fetch(`/api/tasks/${selectedTaskId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            deleteBtn.disabled = false;

            if (res.ok) {
                currentTasks = currentTasks.filter(t => t.id !== selectedTaskId);
                renderTasks();
                updateHeaderTaskCount();
                closeTaskDetailModal();
                window.showToast('Tugas berhasil dihapus!', 'success');
            } else {
                window.showToast('Gagal menghapus tugas.', 'error');
            }
        } catch (e) {
            deleteBtn.disabled = false;
            window.showToast('Terjadi kesalahan koneksi.', 'error');
        }
    }

    // Helper: Escape HTML strings to prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Close modal on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeTaskDetailModal();
        }
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', loadTasks);
</script>
@endpush
