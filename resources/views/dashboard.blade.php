@extends('layouts.app')

@section('title', 'Dashboard - Jara An Advance To-do-List')

@section('content')
<div class="space-y-8">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Daftar List Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan pantau seluruh daftar tugas pribadi Anda (FR-02)</p>
        </div>
        <div>
            <button 
                type="button"
                id="btn-open-add-modal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold shadow-sm hover:shadow shadow-indigo-200 transition-all duration-150 cursor-pointer w-full sm:w-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah List
            </button>
        </div>
    </div>

    <!-- Main Lists Grid Area -->
    <div>
        <!-- Loading State Skeleton -->
        <div id="loading-state" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @for ($i = 0; $i < 3; $i++)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm animate-pulse space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-slate-200"></div>
                        <div class="w-6 h-6 rounded bg-slate-200"></div>
                    </div>
                    <div class="h-5 bg-slate-200 rounded w-3/4"></div>
                    <div class="h-4 bg-slate-100 rounded w-1/2"></div>
                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                        <div class="h-4 bg-slate-200 rounded w-20"></div>
                        <div class="h-4 bg-slate-100 rounded w-16"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Empty State Container -->
        <div id="empty-state" class="hidden bg-white rounded-3xl p-12 text-center border border-dashed border-slate-300 max-w-lg mx-auto my-8 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum Ada List</h3>
            <p class="text-sm text-slate-500 mt-1.5 mb-6 max-w-xs mx-auto">
                Anda belum memiliki list tugas. Buat list pertama Anda untuk mulai mengorganisir tugas dan deadline.
            </p>
            <button 
                type="button" 
                onclick="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat List Pertama
            </button>
        </div>

        <!-- Lists Cards Container -->
        <div id="lists-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Dynamic Cards will be rendered here -->
        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH LIST ================= -->
<div id="modal-add-list" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-0 bg-slate-900/40 backdrop-blur-xs transition-opacity">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Tambah List Baru</h3>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-add-list" onsubmit="submitAddList(event)" class="mt-4 space-y-4">
            <div>
                <label for="add-list-name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama List</label>
                <input 
                    type="text" 
                    id="add-list-name" 
                    name="name" 
                    required 
                    placeholder="misal: Proyek PPK Jara, Sprint 1, Pribadi..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none transition-all"
                >
                <p id="add-list-error" class="hidden text-xs text-rose-600 mt-1.5"></p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button 
                    type="button" 
                    onclick="closeAddModal()" 
                    class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    id="btn-submit-add" 
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold shadow-sm transition-all flex items-center gap-2"
                >
                    <span>Simpan List</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL RENAME LIST ================= -->
<div id="modal-rename-list" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-0 bg-slate-900/40 backdrop-blur-xs transition-opacity">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Ubah Nama List</h3>
            <button type="button" onclick="closeRenameModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-rename-list" onsubmit="submitRenameList(event)" class="mt-4 space-y-4">
            <input type="hidden" id="rename-list-id">
            <div>
                <label for="rename-list-name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Baru</label>
                <input 
                    type="text" 
                    id="rename-list-name" 
                    name="name" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none transition-all"
                >
                <p id="rename-list-error" class="hidden text-xs text-rose-600 mt-1.5"></p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button 
                    type="button" 
                    onclick="closeRenameModal()" 
                    class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    id="btn-submit-rename" 
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-all"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL DELETE CONFIRMATION ================= -->
<div id="modal-delete-list" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-0 bg-slate-900/40 backdrop-blur-xs transition-opacity">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all p-6">
        <div class="flex items-center gap-3 pb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Hapus List</h3>
                <p class="text-xs text-slate-500">Konfirmasi penghapusan permanen</p>
            </div>
        </div>

        <input type="hidden" id="delete-list-id">
        <p class="text-sm text-slate-600 my-4">
            Apakah Anda yakin ingin menghapus list <strong id="delete-list-name" class="text-slate-900"></strong>? Semua tugas di dalam list ini juga akan dihapus secara otomatis.
        </p>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button 
                type="button" 
                onclick="closeDeleteModal()" 
                class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors"
            >
                Batal
            </button>
            <button 
                type="button" 
                id="btn-submit-delete" 
                onclick="submitDeleteList()" 
                class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-sm font-semibold shadow-sm transition-all"
            >
                Hapus Permanen
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let userLists = [];
    let activeDropdownId = null;

    // Fetch lists from GET /api/lists
    async function loadLists() {
        const loadingState = document.getElementById('loading-state');
        const emptyState = document.getElementById('empty-state');
        const listsGrid = document.getElementById('lists-grid');

        loadingState.classList.remove('hidden');
        emptyState.classList.add('hidden');
        listsGrid.classList.add('hidden');

        try {
            const response = await fetch('/api/lists', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();
            loadingState.classList.add('hidden');

            if (response.ok && result.data) {
                userLists = result.data;
                renderLists(userLists);
            } else {
                window.showToast('Gagal memuat daftar list.', 'error');
            }
        } catch (error) {
            loadingState.classList.add('hidden');
            console.error('Error fetching lists:', error);
            window.showToast('Terjadi kesalahan koneksi.', 'error');
        }
    }

    // Render lists in the grid
    function renderLists(lists) {
        const emptyState = document.getElementById('empty-state');
        const listsGrid = document.getElementById('lists-grid');

        if (!lists || lists.length === 0) {
            emptyState.classList.remove('hidden');
            listsGrid.classList.add('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        listsGrid.classList.remove('hidden');

        listsGrid.innerHTML = lists.map(list => {
            const taskCount = list.tasks_count !== undefined ? list.tasks_count : 0;
            const createdDate = list.created_at ? new Date(list.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }) : '-';

            return `
                <div 
                    class="relative bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200 cursor-pointer group flex flex-col justify-between"
                    onclick="navigateToList(${list.id})"
                >
                    <!-- Top Bar: Icon & Dropdown Menu -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-105 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-200 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>

                        <!-- Dropdown Menu Trigger (Action Menu: Rename / Delete) -->
                        <div class="relative" onclick="event.stopPropagation()">
                            <button 
                                type="button" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                                onclick="toggleActionMenu(${list.id})"
                                title="Menu Aksi"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Content -->
                            <div 
                                id="action-menu-${list.id}" 
                                class="hidden absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-slate-200/80 py-1.5 z-20"
                            >
                                <button 
                                    type="button" 
                                    class="w-full text-left px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 flex items-center gap-2 transition-colors"
                                    onclick="openRenameModal(${list.id}, '${escapeHtml(list.name)}')"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Ubah Nama
                                </button>
                                <button 
                                    type="button" 
                                    class="w-full text-left px-3.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition-colors"
                                    onclick="openDeleteModal(${list.id}, '${escapeHtml(list.name)}')"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus List
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Middle: List Name -->
                    <div class="mb-5">
                        <h2 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1 leading-snug">
                            ${escapeHtml(list.name)}
                        </h2>
                        <span class="text-xs text-slate-400 mt-1 block">Dibuat: ${createdDate}</span>
                    </div>

                    <!-- Footer: Task Count & Navigation Indicator -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${taskCount > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600'}">
                            <span class="w-1.5 h-1.5 rounded-full ${taskCount > 0 ? 'bg-indigo-500' : 'bg-slate-400'}"></span>
                            ${taskCount} Tugas
                        </span>

                        <span class="inline-flex items-center text-xs font-semibold text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all">
                            Buka List
                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Navigate to List Detail (/lists/:id)
    function navigateToList(id) {
        window.location.href = `/lists/${id}`;
    }

    // Toggle Dropdown Menu
    function toggleActionMenu(id) {
        const menu = document.getElementById(`action-menu-${id}`);
        if (!menu) return;

        if (activeDropdownId && activeDropdownId !== id) {
            const prev = document.getElementById(`action-menu-${activeDropdownId}`);
            if (prev) prev.classList.add('hidden');
        }

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            activeDropdownId = id;
        } else {
            menu.classList.add('hidden');
            activeDropdownId = null;
        }
    }

    // Close any open action menu when clicking outside
    document.addEventListener('click', () => {
        if (activeDropdownId) {
            const menu = document.getElementById(`action-menu-${activeDropdownId}`);
            if (menu) menu.classList.add('hidden');
            activeDropdownId = null;
        }
    });

    // ================= MODAL HANDLERS ================= //

    // Add Modal
    const addModal = document.getElementById('modal-add-list');
    const openAddBtn = document.getElementById('btn-open-add-modal');

    function openAddModal() {
        document.getElementById('add-list-name').value = '';
        document.getElementById('add-list-error').classList.add('hidden');
        addModal.classList.remove('hidden');
        addModal.classList.add('flex');
        setTimeout(() => document.getElementById('add-list-name').focus(), 100);
    }

    function closeAddModal() {
        addModal.classList.add('hidden');
        addModal.classList.remove('flex');
    }

    if (openAddBtn) {
        openAddBtn.addEventListener('click', openAddModal);
    }

    async function submitAddList(event) {
        event.preventDefault();
        const input = document.getElementById('add-list-name');
        const errorEl = document.getElementById('add-list-error');
        const submitBtn = document.getElementById('btn-submit-add');
        const name = input.value.trim();

        if (!name) {
            errorEl.textContent = 'Nama list wajib diisi.';
            errorEl.classList.remove('hidden');
            return;
        }

        errorEl.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75');

        try {
            const res = await fetch('/api/lists', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name })
            });

            const data = await res.json();
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');

            if (res.ok && data.data) {
                closeAddModal();
                window.showToast('List berhasil ditambahkan!', 'success');
                loadLists();
            } else {
                errorEl.textContent = data.message || 'Gagal menyimpan list.';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');
            errorEl.textContent = 'Terjadi kesalahan sistem.';
            errorEl.classList.remove('hidden');
        }
    }

    // Rename Modal
    const renameModal = document.getElementById('modal-rename-list');

    function openRenameModal(id, currentName) {
        document.getElementById('rename-list-id').value = id;
        document.getElementById('rename-list-name').value = currentName;
        document.getElementById('rename-list-error').classList.add('hidden');
        renameModal.classList.remove('hidden');
        renameModal.classList.add('flex');
        setTimeout(() => document.getElementById('rename-list-name').focus(), 100);
    }

    function closeRenameModal() {
        renameModal.classList.add('hidden');
        renameModal.classList.remove('flex');
    }

    async function submitRenameList(event) {
        event.preventDefault();
        const id = document.getElementById('rename-list-id').value;
        const input = document.getElementById('rename-list-name');
        const errorEl = document.getElementById('rename-list-error');
        const submitBtn = document.getElementById('btn-submit-rename');
        const name = input.value.trim();

        if (!name) {
            errorEl.textContent = 'Nama list tidak boleh kosong.';
            errorEl.classList.remove('hidden');
            return;
        }

        errorEl.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75');

        try {
            const res = await fetch(`/api/lists/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name })
            });

            const data = await res.json();
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');

            if (res.ok) {
                closeRenameModal();
                window.showToast('Nama list berhasil diubah!', 'success');
                loadLists();
            } else {
                errorEl.textContent = data.message || 'Gagal mengubah nama list.';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');
            errorEl.textContent = 'Terjadi kesalahan sistem.';
            errorEl.classList.remove('hidden');
        }
    }

    // Delete Modal
    const deleteModal = document.getElementById('modal-delete-list');

    function openDeleteModal(id, name) {
        document.getElementById('delete-list-id').value = id;
        document.getElementById('delete-list-name').textContent = name;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
    }

    async function submitDeleteList() {
        const id = document.getElementById('delete-list-id').value;
        const submitBtn = document.getElementById('btn-submit-delete');

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75');

        try {
            const res = await fetch(`/api/lists/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');

            if (res.ok) {
                closeDeleteModal();
                window.showToast('List berhasil dihapus!', 'success');
                loadLists();
            } else {
                const data = await res.json();
                window.showToast(data.message || 'Gagal menghapus list.', 'error');
            }
        } catch (e) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');
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

    // Keyboard navigation (Esc key to close modals)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAddModal();
            closeRenameModal();
            closeDeleteModal();
        }
    });

    // Initial Load
    document.addEventListener('DOMContentLoaded', loadLists);
</script>
@endpush

