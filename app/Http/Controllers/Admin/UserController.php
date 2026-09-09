<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh akun pengguna.
     */
    public function index(): View
    {
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form pembuatan pengguna baru oleh Admin.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan akun pengguna baru ciptaan Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['admin', 'user'])],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna ' . $validated['name'] . ' berhasil ditambahkan.');
    }

    /**
     * Menghapus akun pengguna dari sistem dengan proteksi keamanan.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Guard 1: Menolak penghapusan akun milik sendiri yang sedang aktif login
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tindakan ditolak: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // Guard 2: Mencegah sistem kehabisan akun Admin (Last Admin Guard)
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tindakan ditolak: Tidak dapat menghapus admin terakhir di dalam sistem.');
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun ' . $userName . ' berhasil dihapus dari sistem.');
    }
}