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
use App\Support\FrontendUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::view('/', 'home')->name('home');

Route::post('/angular-login', [AuthenticatedSessionController::class, 'store'])->name('angular.login');
Route::post('/angular-register', [RegisteredUserController::class, 'store'])->name('angular.register');

Route::middleware('guest')->group(function () {
    Route::get('/register', fn () => FrontendUrl::shouldServeAngularShell()
        ? view('angular.shell')
        : redirect()->away(FrontendUrl::angular('/register')))->name('register');
    Route::get('/login', fn () => FrontendUrl::shouldServeAngularShell()
        ? view('angular.shell')
        : redirect()->away(FrontendUrl::angular('/login')))->name('login');
});

Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]));

Route::middleware('auth')->group(function () {
    Route::get('/angular-session', function () {
        $user = auth()->user()->load(['klijent', 'zaposleni']);
        $token = $user->api_token ?: Str::random(80);

        if ($user->api_token !== $token) {
            $user->forceFill(['api_token' => $token])->save();
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('klijenti', KlijentController::class)->parameters(['klijenti' => 'klijent']);
    Route::resource('zaposleni', ZaposleniController::class)->parameters(['zaposleni' => 'zaposleni']);
    Route::resource('usluge', UslugaController::class)->parameters(['usluge' => 'usluge']);

    Route::get('termini/create', fn () => FrontendUrl::shouldServeAngularShell()
        ? view('angular.shell')
        : redirect()->away(FrontendUrl::angular('/termini/create')))->name('termini.create');

    Route::patch('termini/{termin}/otkazi', [TerminController::class, 'cancel'])->name('termini.cancel');
    Route::resource('termini', TerminController::class)
        ->except(['create'])
        ->parameters(['termini' => 'termini']);

    Route::get('tretmani/{tretman}/fotografije/{fotografija}', [EvidencijaTretmanaController::class, 'photo'])
        ->name('tretmani.fotografije.show');
    Route::resource('tretmani', EvidencijaTretmanaController::class)
        ->parameters(['tretmani' => 'tretman']);

    Route::resource('racuni', RacunController::class)->parameters(['racuni' => 'racuni']);
    Route::resource('uplate', UplataController::class)->parameters(['uplate' => 'uplate']);
    Route::resource('podsetnici', PodsetnikController::class)->parameters(['podsetnici' => 'podsetnici']);

    Route::get('/izvestaji', IzvestajController::class)->middleware('role:administrator')->name('izvestaji.index');
});
