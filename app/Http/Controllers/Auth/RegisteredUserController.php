<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Klijent;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ime' => ['required', 'string', 'max:255'],
            'prezime' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'adresa' => ['nullable', 'string', 'max:255'],
            'datum_rodjenja' => ['nullable', 'date'],
            'preferencije' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['ime'].' '.$validated['prezime'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_KLIJENT,
            ]);

            Klijent::create([
                'user_id' => $user->id,
                'ime' => $validated['ime'],
                'prezime' => $validated['prezime'],
                'telefon' => $validated['telefon'] ?? null,
                'adresa' => $validated['adresa'] ?? null,
                'datum_rodjenja' => $validated['datum_rodjenja'] ?? null,
                'preferencije' => $validated['preferencije'] ?? null,
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Registracija je uspesna. Korisnik je sacuvan.');
    }
}
