<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [UserDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::group(['prefix' => 'user'], function () {
        Route::get('{user_id}/tokens/create', [TokenController::class, 'store'])->name('user.tokens.create');
        Route::get('{user_id}/tokens/{token_id}/delete', [TokenController::class, 'delete'])->name('user.tokens.delete');
    });
});

require __DIR__ . '/auth.php';
