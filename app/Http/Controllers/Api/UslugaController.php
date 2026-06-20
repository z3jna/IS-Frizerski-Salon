<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usluga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UslugaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Usluga::orderBy('tip_usluge')->orderBy('naziv')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $usluga = Usluga::create($this->validated($request));

        return response()->json(['message' => 'Usluga je dodata.', 'data' => $usluga], 201);
    }

    public function show(Usluga $usluge): JsonResponse
    {
        return response()->json(['data' => $usluge]);
    }

    public function update(Request $request, Usluga $usluge): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $usluge->update($this->validated($request));

        return response()->json(['message' => 'Usluga je sacuvana.', 'data' => $usluge]);
    }

    public function destroy(Request $request, Usluga $usluge): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $usluge->delete();

        return response()->json(['message' => 'Usluga je obrisana.']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'naziv' => ['required', 'string', 'max:255'],
            'tip_usluge' => ['required', 'string', 'max:255'],
            'opis' => ['nullable', 'string'],
            'trajanje_minuta' => ['required', 'integer', 'min:10', 'max:600'],
            'cena' => ['required', 'numeric', 'min:0'],
            'dostupnost' => ['nullable', 'boolean'],
        ]) + ['dostupnost' => false];
    }
}
