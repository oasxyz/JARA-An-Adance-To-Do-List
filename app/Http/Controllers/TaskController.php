<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Anggota & Pemilik membuat tugas baru dalam list - FR-03, FR-09
     */
    public function store(Request $request, TodoList $list)
    {
        $userId = Auth::id();

        // Otorisasi: Harus pemilik atau anggota list
        if (!$list->hasAccess($userId)) {
            abort(403, 'Akses ditolak. Anda bukan anggota dari list ini.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:Low,Medium,High'],
            'deadline' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'deadline' => $validated['deadline'],
            'status' => 'not done',
            'assignee_id' => $validated['assignee_id'],
            'created_by' => $userId,
        ]);

        return back()->with('success', 'Tugas berhasil ditambahkan ke dalam list!');
    }

    /**
     * Anggota & Pemilik mengubah status tugas (done / not done / canceled) - FR-06, FR-09
     */
    public function updateStatus(Request $request, Task $task)
    {
        $userId = Auth::id();

        if (!$task->list->hasAccess($userId)) {
            abort(403, 'Akses ditolak. Anda bukan anggota dari list ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:done,not done,canceled'],
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Status tugas '{$task->title}' diperbarui menjadi: {$task->status}");
    }

    /**
     * Update tugas secara menyeluruh (assignee, priority, deadline, title) - FR-04, FR-05, FR-09
     */
    public function update(Request $request, Task $task)
    {
        $userId = Auth::id();

        if (!$task->list->hasAccess($userId)) {
            abort(403, 'Akses ditolak. Anda bukan anggota dari list ini.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:Low,Medium,High'],
            'deadline' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:done,not done,canceled'],
        ]);

        $task->update($validated);

        return back()->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Hapus tugas dari list
     */
    public function destroy(Task $task)
    {
        $userId = Auth::id();

        if (!$task->list->hasAccess($userId)) {
            abort(403, 'Akses ditolak. Anda bukan anggota dari list ini.');
        }

        $task->delete();

        return back()->with('success', 'Tugas berhasil dihapus dari list.');
    }
}
