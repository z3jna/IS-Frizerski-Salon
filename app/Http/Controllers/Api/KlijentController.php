<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Klijent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KlijentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isZaposleni(), 403);

        return response()->json([
            'data' => Klijent::with('user')->orderBy('prezime')->orderBy('ime')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $this->validated($request);
        $klijent = DB::transaction(function () use ($data) {
            return Klijent::create($this->withUserData($data, User::ROLE_KLIJENT))->load('user');
        });

        return response()->json(['message' => 'Klijent je dodat.', 'data' => $klijent], 201);
    }

    public function show(Request $request, Klijent $klijenti): JsonResponse
    {
        $this->authorizeAccess($request, $klijenti);

        return response()->json([
            'data' => $klijenti->load(['user', 'termini.usluga', 'termini.zaposleni', 'podsetnici']),
        ]);
    }

    public function update(Request $request, Klijent $klijenti): JsonResponse
    {
        $this->authorizeAccess($request, $klijenti);

        $data = $this->validated($request, $klijenti);
        DB::transaction(function () use ($data, $klijenti) {
            $klijenti->update($this->withUserData($data, User::ROLE_KLIJENT, $klijenti->user));
        });

        return response()->json(['message' => 'Podaci klijenta su sacuvani.', 'data' => $klijenti->fresh('user')]);
    }

    public function destroy(Request $request, Klijent $klijenti): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $klijenti->delete();

        return response()->json(['message' => 'Klijent je obrisan.']);
    }

    private function validated(Request $request, ?Klijent $klijent = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('klijenti', 'user_id')->ignore($klijent)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($klijent?->user_id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'ime' => ['required', 'string', 'max:255'],
            'prezime' => ['required', 'string', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'adresa' => ['nullable', 'string', 'max:255'],
            'datum_rodjenja' => ['nullable', 'date'],
            'napomena' => ['nullable', 'string'],
            'preferencije' => ['nullable', 'string'],
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

    private function authorizeAccess(Request $request, Klijent $klijent): void
    {
        $user = $request->user();

        abort_unless($user->isAdmin() || $user->isZaposleni() || $user->klijent?->is($klijent), 403);
    }
}
