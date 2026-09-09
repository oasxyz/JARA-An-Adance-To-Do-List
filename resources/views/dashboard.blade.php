@extends('layouts.app')

@section('title', 'Dashboard - Jara An Advance To-do-List')

@section('content')
<div class="space-y-8">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Halo, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan pantau seluruh daftar tugas pribadi Anda</p>
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

    <!-- Sisa seluruh komponen: loading state, empty state, lists grid, modal-modal, dan script JS tetap berada di bawah sini -->