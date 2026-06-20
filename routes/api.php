<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvidencijaTretmanaController;
use App\Http\Controllers\Api\KlijentController;
use App\Http\Controllers\Api\RacunController;
use App\Http\Controllers\Api\TerminController;
use App\Http\Controllers\Api\UslugaController;
use App\Http\Controllers\Api\ZaposleniController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/usluge', [UslugaController::class, 'index']);
Route::get('/usluge/{usluge}', [UslugaController::class, 'show']);

Route::middleware('api.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/klijenti', [KlijentController::class, 'index']);
    Route::post('/klijenti', [KlijentController::class, 'store']);
    Route::get('/klijenti/{klijenti}', [KlijentController::class, 'show']);
    Route::put('/klijenti/{klijenti}', [KlijentController::class, 'update']);
    Route::patch('/klijenti/{klijenti}', [KlijentController::class, 'update']);
    Route::delete('/klijenti/{klijenti}', [KlijentController::class, 'destroy']);

    Route::post('/usluge', [UslugaController::class, 'store']);
    Route::put('/usluge/{usluge}', [UslugaController::class, 'update']);
    Route::patch('/usluge/{usluge}', [UslugaController::class, 'update']);
    Route::delete('/usluge/{usluge}', [UslugaController::class, 'destroy']);

    Route::get('/zaposleni', [ZaposleniController::class, 'index']);
    Route::post('/zaposleni', [ZaposleniController::class, 'store']);
    Route::get('/zaposleni/{zaposleni}', [ZaposleniController::class, 'show']);
    Route::put('/zaposleni/{zaposleni}', [ZaposleniController::class, 'update']);
    Route::patch('/zaposleni/{zaposleni}', [ZaposleniController::class, 'update']);
    Route::delete('/zaposleni/{zaposleni}', [ZaposleniController::class, 'destroy']);

    Route::get('/dostupni-termini', [TerminController::class, 'dostupni']);
    Route::get('/termini/klijent/{klijent_id}', [TerminController::class, 'forKlijent']);
    Route::get('/termini/zaposleni/{zaposleni_id}', [TerminController::class, 'forZaposleni']);
    Route::get('/termini', [TerminController::class, 'index']);
    Route::post('/termini', [TerminController::class, 'store']);
    Route::get('/termini/{termini}', [TerminController::class, 'show']);
    Route::put('/termini/{termini}', [TerminController::class, 'update']);
    Route::patch('/termini/{termini}', [TerminController::class, 'update']);
    Route::delete('/termini/{termini}', [TerminController::class, 'destroy']);

    Route::get('/tretmani', [EvidencijaTretmanaController::class, 'index']);
    Route::post('/tretmani', [EvidencijaTretmanaController::class, 'store']);
    Route::get('/tretmani/{tretmani}', [EvidencijaTretmanaController::class, 'show']);
    Route::put('/tretmani/{tretmani}', [EvidencijaTretmanaController::class, 'update']);
    Route::patch('/tretmani/{tretmani}', [EvidencijaTretmanaController::class, 'update']);
    Route::delete('/tretmani/{tretmani}', [EvidencijaTretmanaController::class, 'destroy']);

    Route::get('/racuni', [RacunController::class, 'index']);
    Route::post('/racuni', [RacunController::class, 'store']);
    Route::get('/racuni/{racuni}', [RacunController::class, 'show']);
    Route::put('/racuni/{racuni}', [RacunController::class, 'update']);
    Route::patch('/racuni/{racuni}', [RacunController::class, 'update']);
    Route::delete('/racuni/{racuni}', [RacunController::class, 'destroy']);
});
