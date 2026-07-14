<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TerminController;
use App\Http\Controllers\Api\UslugaController;
use App\Http\Controllers\Api\ZaposleniController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('web')
    ->withoutMiddleware(ValidateCsrfToken::class);
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('web')
    ->withoutMiddleware(ValidateCsrfToken::class);

Route::middleware('api.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('web')
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/usluge', [UslugaController::class, 'index']);
    Route::get('/zaposleni', [ZaposleniController::class, 'index']);
    Route::get('/dostupni-termini', [TerminController::class, 'dostupni']);
    Route::post('/termini', [TerminController::class, 'store']);
});
