<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TerminController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('api.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::prefix('zakazivanje')->group(function () {
        Route::get('/opcije', [TerminController::class, 'options']);
        Route::get('/dostupni-termini', [TerminController::class, 'dostupni']);
        Route::post('/termini', [TerminController::class, 'store']);
    });
});
