<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

/**
 * Redirects the '/login' route to the '/note' route with the name 'dashboard'
 */
Route::redirect('/', '/note') -> name('dashboard');

/**
 * Authenticated and verified users only
 */
Route::middleware(['auth', 'verified']) -> group(function () {
    /**
     * RESTful resource controller for notes
     */
    Route::resource('note', NoteController::class);
});

/**
 * Authenticated users only
 */
Route::middleware('auth')->group(function () {
    /**
     * Edit user profile
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    /**
     * Update user profile
     */
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /**
     * Delete user profile
     */
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Auth routes
 */
require __DIR__.'/auth.php';
