<?php

use App\Http\Controllers\Auth\TuiterApiController;
use App\Http\Middleware\ApplicationMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([ApplicationMiddleware::class])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('/login', [TuiterApiController::class, 'login'])->name('me.login');
        Route::get('/me/feed', [TuiterApiController::class, 'feed'])->name('me.feed');
        Route::get('/me/profile', [TuiterApiController::class, 'profile'])->name('me.profile');
        Route::get('/me/tuits/{tuit_id}', [TuiterApiController::class, 'showTuit'])->name('tuits.show');
        Route::post('/me/tuits', [TuiterApiController::class, 'createTuit'])->name('tuits.create');
        Route::post('/me/tuits/{tuit_id}/likes', [TuiterApiController::class, 'addLike'])->name('tuits.likes.add');
        Route::delete('/me/tuits/{tuit_id}/likes', [TuiterApiController::class, 'removeLike'])->name('tuits.likes.add');
    });
});