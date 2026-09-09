<?php

use App\Modules\List\Controllers\TodoListController;
use App\Modules\Task\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Jara An Adance To-Do-List (FR-02 - FR-06)
|--------------------------------------------------------------------------
|
| Middleware 'mock.auth' provides stub user resolution until Yustinus
| finishes the official authentication system.
|
*/

Route::middleware(['mock.auth'])->group(function () {
    // List Endpoints (FR-02)
    Route::get('/lists', [TodoListController::class, 'index'])->name('lists.index');
    Route::get('/lists/{id}', [TodoListController::class, 'show'])->name('lists.show_api');
    Route::post('/lists', [TodoListController::class, 'store'])->name('lists.store');
    Route::put('/lists/{id}', [TodoListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{id}', [TodoListController::class, 'destroy'])->name('lists.destroy');

    // Task Endpoints (FR-03 to FR-06)
    Route::get('/lists/{list_id}/tasks', [TaskController::class, 'index'])->name('lists.tasks.index');
    Route::post('/lists/{list_id}/tasks', [TaskController::class, 'store'])->name('lists.tasks.store');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});
