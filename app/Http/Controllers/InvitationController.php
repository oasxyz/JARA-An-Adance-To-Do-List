<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Pemilik list membuat undangan (Email / Link) - FR-07
     */
    public function store(Request $request, TodoList $list)
    {
        // Otorisasi: Anggota non-owner tidak bisa mengundang orang (SRS Non-functional requirements)
        if (!$list->isOwner(Auth::id())) {
            abort(403, 'Hanya pemilik list yang dapat mengundang anggota baru.');
        }

        $validated = $request->validate([
            'invited_email' => ['nullable', 'email'],
        ]);

        $token = Str::random(32);

        $invitation = Invitation::create([
            'list_id' => $list->id,
            'invited_email' => $validated['invited_email'] ?? null,
            'invited_by' => Auth::id(),
            'token' => $token,
            'status' => 'pending',
        ]);

        $inviteUrl = route('invitations.show', $token);

        return back()->with([
            'success' => 'Undangan berhasil dibuat!',
            'generated_invite_url' => $inviteUrl,
            'invited_email' => $invitation->invited_email,
        ]);
    }

    /**
     * Halaman konfirmasi undangan (Lihat detail list & pengundang) - FR-08
     */
    public function show($token)
    {
        $invitation = Invitation::with(['list.owner', 'inviter'])
            ->where('token', $token)
            ->firstOrFail();

        return view('invitations.show', compact('invitation'));
    }

    /**
     * User menerima undangan - FR-08
     */
    public function accept($token)
    {
        $invitation = Invitation::with('list')->where('token', $token)->firstOrFail();
        $user = Auth::user();
        $list = $invitation->list;

        if ($invitation->status === 'accepted') {
            return redirect()->route('lists.show', $list->id)
                ->with('info', 'Undangan ini sudah pernah Anda terima sebelumnya.');
        }

        // Tambahkan ke anggota list jika belum terdaftar
        if (!$list->isOwner($user->id) && !$list->isMember($user->id)) {
            $list->members()->attach($user->id, ['joined_at' => now()]);
        }

        // Tandai undangan diterima
        $invitation->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        return redirect()->route('lists.show', $list->id)
            ->with('success', "Selamat! Anda telah bergabung ke dalam list '{$list->name}'.");
    }

    /**
     * User menolak undangan - FR-08
     */
    public function reject($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        $invitation->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('info', "Anda telah menolak undangan untuk bergabung ke list '{$invitation->list->name}'. Alur pengerjaan berhenti.");
    }
}
