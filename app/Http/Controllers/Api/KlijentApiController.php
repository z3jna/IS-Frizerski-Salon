<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Klijent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KlijentApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Klijent::with('user')->orderBy('prezime')->orderBy('ime')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        $klijent = Klijent::create($data)->load('user');

        return response()->json(['message' => 'Klijent je sacuvan.', 'data' => $klijent], 201);
    }

    public function show(Klijent $klijent): JsonResponse
    {
        return response()->json(['data' => $klijent->load(['user', 'termini.usluga'])]);
    }

    public function update(Request $request, Klijent $klijent): JsonResponse
    {
        $klijent->update($this->validateData($request, $klijent));

        return response()->json(['message' => 'Klijent je izmenjen.', 'data' => $klijent->fresh()->load('user')]);
    }

    public function destroy(Klijent $klijent): JsonResponse
    {
        $klijent->delete();

        return response()->json(['message' => 'Klijent je obrisan.']);
    }

    private function validateData(Request $request, ?Klijent $klijent = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('klijenti', 'user_id')->ignore($klijent)],
            'ime' => ['required', 'string', 'max:255'],
            'prezime' => ['required', 'string', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'adresa' => ['nullable', 'string', 'max:255'],
            'datum_rodjenja' => ['nullable', 'date'],
            'napomena' => ['nullable', 'string'],
            'preferencije' => ['nullable', 'string'],
        ]);
    }
}
