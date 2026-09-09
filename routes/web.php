<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Fitur yang butuh login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Quick switch akun khusus kemudahan demo praktikum
    Route::get('/switch-user/{id}', [AuthController::class, 'switchUser'])->name('auth.switch');

    // Dashboard & List Management (FR-02)
    Route::get('/dashboard', [TodoListController::class, 'index'])->name('dashboard');
    Route::post('/lists', [TodoListController::class, 'store'])->name('lists.store');
    Route::get('/lists/{list}', [TodoListController::class, 'show'])->name('lists.show');
    Route::delete('/lists/{list}', [TodoListController::class, 'destroy'])->name('lists.destroy');

    // Task Collaboration (FR-03, FR-04, FR-05, FR-06, FR-09)
    Route::post('/lists/{list}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update_status');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Invitation System (FR-07, FR-08)
    Route::post('/lists/{list}/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/reject', [InvitationController::class, 'reject'])->name('invitations.reject');
});
