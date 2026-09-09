<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Modules\List\Models\TodoList;
use Illuminate\Support\Facades\Route;

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Routes (Public Auth - Yustinus)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes (Semua User Terdaftar)
Route::middleware('auth')->group(function () {
    // Tampilan Dashboard List Tugas
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Detail List Tugas (Shifa)
    Route::get('/lists/{id}', function ($id) {
        $list = TodoList::withCount('tasks')->findOrFail($id);
        return view('lists.show', compact('list'));
    })->name('lists.show');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes (Yustinus)
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});