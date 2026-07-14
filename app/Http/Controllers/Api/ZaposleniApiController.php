<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zaposleni;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZaposleniApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Zaposleni::with('user')->orderBy('prezime')->orderBy('ime')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $zaposleni = Zaposleni::create($this->validateData($request))->load('user');

        return response()->json(['message' => 'Zaposleni je sacuvan.', 'data' => $zaposleni], 201);
    }

    public function show(Zaposleni $zaposleni): JsonResponse
    {
        return response()->json(['data' => $zaposleni->load(['user', 'termini.usluga', 'termini.klijent'])]);
    }

    public function update(Request $request, Zaposleni $zaposleni): JsonResponse
    {
        $zaposleni->update($this->validateData($request, $zaposleni));

        return response()->json(['message' => 'Zaposleni je izmenjen.', 'data' => $zaposleni->fresh()->load('user')]);
    }

    public function destroy(Zaposleni $zaposleni): JsonResponse
    {
        $zaposleni->delete();

        return response()->json(['message' => 'Zaposleni je obrisan.']);
    }

    private function validateData(Request $request, ?Zaposleni $zaposleni = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('zaposleni', 'user_id')->ignore($zaposleni)],
            'ime' => ['required', 'string', 'max:255'],
            'prezime' => ['required', 'string', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'pozicija' => ['nullable', 'string', 'max:255'],
            'radno_vreme' => ['nullable', 'string', 'max:255'],
            'datum_zaposlenja' => ['nullable', 'date'],
            'plata' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
