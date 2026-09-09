<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/lists/{id}', function ($id) {
    return view('lists.show', ['listId' => $id]);
})->name('lists.show');
