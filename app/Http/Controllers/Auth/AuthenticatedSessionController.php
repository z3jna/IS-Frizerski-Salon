<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Uneti kredencijali nisu ispravni.',
                    'errors' => ['email' => ['Uneti kredencijali nisu ispravni.']],
                ], 422);
            }

            return back()
                ->withErrors(['email' => 'Uneti kredencijali nisu ispravni.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $request->user()->load(['klijent', 'zaposleni']);
        $token = Str::random(80);
        $user->forceFill(['api_token' => $token])->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Prijava je uspesna.',
                'redirect' => route('dashboard'),
                'token' => $token,
                'user' => $user,
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
