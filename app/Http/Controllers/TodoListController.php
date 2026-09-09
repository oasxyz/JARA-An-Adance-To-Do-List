<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoListController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. List milik saya
        $ownedLists = $user->ownedLists()->withCount('tasks')->latest()->get();

        // 2. List yang saya ikuti sebagai anggota
        $memberLists = $user->memberLists()->with('owner')->withCount('tasks')->latest()->get();

        // 3. Undangan pending yang ditujukan ke email saya
        $pendingInvitations = Invitation::with(['list.owner', 'inviter'])
            ->where('invited_email', $user->email)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('lists.index', compact('ownedLists', 'memberLists', 'pendingInvitations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $list = TodoList::create([
            'name' => $validated['name'],
            'owner_id' => Auth::id(),
        ]);

        return redirect()->route('lists.show', $list->id)->with('success', 'List berhasil dibuat!');
    }

    public function show(TodoList $list)
    {
        $userId = Auth::id();

        // Otorisasi: Hanya pemilik atau anggota yang boleh melihat isi list
        if (!$list->hasAccess($userId)) {
            abort(403, 'Akses ditolak. Anda bukan pemilik ataupun anggota dari list ini.');
        }

        // Daftar undangan jika user adalah pemilik
        $invitations = $list->isOwner($userId)
            ? $list->invitations()->with('inviter')->latest()->get()
            : collect();

        $isOwner = $list->isOwner($userId);

        return view('lists.show', compact('list', 'invitations', 'isOwner'));
    }

    public function destroy(TodoList $list)
    {
        // Otorisasi: Anggota non-owner TIDAK BISA menghapus list (SRS Non-functional requirements)
        if (!$list->isOwner(Auth::id())) {
            abort(403, 'Hanya pemilik list yang berhak menghapus list ini.');
        }

        $list->delete();

        return redirect()->route('dashboard')->with('success', 'List berhasil dihapus.');
    }
}
