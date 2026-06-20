<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zaposleni;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ZaposleniController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => Zaposleni::with('user')->orderBy('prezime')->orderBy('ime')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $zaposleni = Zaposleni::create($this->withUserData($this->validated($request), User::ROLE_ZAPOSLENI))->load('user');

        return response()->json(['message' => 'Zaposleni je dodat.', 'data' => $zaposleni], 201);
    }

    public function show(Request $request, Zaposleni $zaposleni): JsonResponse
    {
        return response()->json([
            'data' => $zaposleni->load(['user', 'termini.klijent', 'termini.usluga']),
        ]);
    }

    public function update(Request $request, Zaposleni $zaposleni): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $zaposleni->update($this->withUserData($this->validated($request, $zaposleni), User::ROLE_ZAPOSLENI, $zaposleni->user));

        return response()->json(['message' => 'Podaci zaposlenog su sacuvani.', 'data' => $zaposleni->fresh('user')]);
    }

    public function destroy(Request $request, Zaposleni $zaposleni): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $zaposleni->delete();

        return response()->json(['message' => 'Zaposleni je obrisan.']);
    }

    private function validated(Request $request, ?Zaposleni $zaposleni = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('zaposleni', 'user_id')->ignore($zaposleni)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($zaposleni?->user_id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'ime' => ['required', 'string', 'max:255'],
            'prezime' => ['required', 'string', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'pozicija' => ['nullable', 'string', 'max:255'],
            'radno_vreme' => ['nullable', 'string', 'max:255'],
            'datum_zaposlenja' => ['nullable', 'date'],
            'plata' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function withUserData(array $data, string $role, ?User $existingUser = null): array
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        unset($data['email'], $data['password'], $data['password_confirmation']);

        $user = $existingUser ?? (! empty($data['user_id']) ? User::find($data['user_id']) : null);

        if (! $user && $email) {
            $user = User::create([
                'name' => $data['ime'].' '.$data['prezime'],
                'email' => $email,
                'password' => Hash::make($password ?: 'password'),
                'role' => $role,
            ]);
        }

        if ($user) {
            $user->fill([
                'name' => $data['ime'].' '.$data['prezime'],
                'email' => $email ?: $user->email,
                'role' => $role,
            ]);

            if ($password) {
                $user->password = Hash::make($password);
            }

            $user->save();
            $data['user_id'] = $user->id;
        }

        return $data;
    }
}
