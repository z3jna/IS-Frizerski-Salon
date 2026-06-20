<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usluga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UslugaApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Usluga::orderBy('naziv')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $usluga = Usluga::create($this->validateData($request));

        return response()->json(['message' => 'Usluga je sacuvana.', 'data' => $usluga], 201);
    }

    public function show(Usluga $usluge): JsonResponse
    {
        return response()->json(['data' => $usluge]);
    }

    public function update(Request $request, Usluga $usluge): JsonResponse
    {
        $usluge->update($this->validateData($request));

        return response()->json(['message' => 'Usluga je izmenjena.', 'data' => $usluge->fresh()]);
    }

    public function destroy(Usluga $usluge): JsonResponse
    {
        $usluge->delete();

        return response()->json(['message' => 'Usluga je obrisana.']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'naziv' => ['required', 'string', 'max:255'],
            'tip_usluge' => ['required', 'string', 'max:255'],
            'opis' => ['nullable', 'string'],
            'trajanje_minuta' => ['required', 'integer', 'min:5', 'max:480'],
            'cena' => ['required', 'numeric', 'min:0'],
            'dostupnost' => ['sometimes', 'boolean'],
        ]);
    }
}
