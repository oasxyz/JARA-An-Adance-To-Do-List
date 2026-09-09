<?php

use App\Modules\List\Models\TodoList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/lists/{id}', function ($id) {
    $list = TodoList::withCount('tasks')->findOrFail($id);

    return view('lists.show', compact('list'));
})->name('lists.show');
