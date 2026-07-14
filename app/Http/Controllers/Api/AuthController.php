<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Klijent;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
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

        $token = Str::random(80);

        $user = DB::transaction(function () use ($validated, $token) {
            $user = User::create([
                'name' => $validated['ime'].' '.$validated['prezime'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_KLIJENT,
                'api_token' => $token,
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

            return $user->load(['klijent', 'zaposleni']);
        });

        event(new Registered($user));
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Registracija je uspesna.',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Uneti kredencijali nisu ispravni.',
                'errors' => ['email' => ['Uneti kredencijali nisu ispravni.']],
            ], 422);
        }

        $request->session()->regenerate();

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = Str::random(80);
        $user->forceFill(['api_token' => $token])->save();

        return response()->json([
            'message' => 'Prijava je uspesna.',
            'token' => $token,
            'user' => $user->load(['klijent', 'zaposleni']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->forceFill(['api_token' => null])->save();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Odjava je uspesna.']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['klijent', 'zaposleni']),
        ]);
    }
}
