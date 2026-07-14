<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvidencijaTretmana;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TretmanApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => EvidencijaTretmana::with(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'fotografije'])->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $tretman = EvidencijaTretmana::create($this->validateData($request))->load(['termin', 'fotografije']);

        return response()->json(['message' => 'Tretman je sacuvan.', 'data' => $tretman], 201);
    }

    public function show(EvidencijaTretmana $tretman): JsonResponse
    {
        return response()->json(['data' => $tretman->load(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'fotografije'])]);
    }

    public function update(Request $request, EvidencijaTretmana $tretman): JsonResponse
    {
        $tretman->update($this->validateData($request));

        return response()->json(['message' => 'Tretman je izmenjen.', 'data' => $tretman->fresh()->load(['termin', 'fotografije'])]);
    }

    public function destroy(EvidencijaTretmana $tretman): JsonResponse
    {
        $tretman->delete();

        return response()->json(['message' => 'Tretman je obrisan.']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'termin_id' => ['required', 'exists:termini,id'],
            'datum' => ['required', 'date'],
            'opis_tretmana' => ['required', 'string'],
            'nijansa' => ['nullable', 'string', 'max:255'],
            'proizvodjac' => ['nullable', 'string', 'max:255'],
            'formula' => ['nullable', 'string'],
            'korisceni_preparati' => ['nullable', 'string'],
            'napomena' => ['nullable', 'string'],
        ]);
    }
}
