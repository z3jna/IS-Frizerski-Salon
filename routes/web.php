<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvidencijaTretmanaController;
use App\Http\Controllers\IzvestajController;
use App\Http\Controllers\KlijentController;
use App\Http\Controllers\PodsetnikController;
use App\Http\Controllers\RacunController;
use App\Http\Controllers\TerminController;
use App\Http\Controllers\UplataController;
use App\Http\Controllers\UslugaController;
use App\Http\Controllers\ZaposleniController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('klijenti', KlijentController::class)->parameters(['klijenti' => 'klijent']);
    Route::resource('zaposleni', ZaposleniController::class)->parameters(['zaposleni' => 'zaposleni']);
    Route::resource('usluge', UslugaController::class)->parameters(['usluge' => 'usluge']);

    Route::patch('termini/{termin}/otkazi', [TerminController::class, 'cancel'])->name('termini.cancel');
    Route::resource('termini', TerminController::class)->parameters(['termini' => 'termini']);

    Route::get('tretmani/{tretman}/fotografije/{fotografija}', [EvidencijaTretmanaController::class, 'photo'])
        ->name('tretmani.fotografije.show');
    Route::resource('tretmani', EvidencijaTretmanaController::class)
        ->parameters(['tretmani' => 'tretman']);

    Route::resource('racuni', RacunController::class)->parameters(['racuni' => 'racuni']);
    Route::resource('uplate', UplataController::class)->parameters(['uplate' => 'uplate']);
    Route::resource('podsetnici', PodsetnikController::class)->parameters(['podsetnici' => 'podsetnici']);

    Route::get('/izvestaji', IzvestajController::class)->middleware('role:administrator')->name('izvestaji.index');
});
