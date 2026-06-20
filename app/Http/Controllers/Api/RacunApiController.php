<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Racun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RacunApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Racun::with(['termin.klijent', 'termin.usluga', 'uplate'])->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $racun = Racun::create($this->validateData($request))->load(['termin', 'uplate']);

        return response()->json(['message' => 'Racun je sacuvan.', 'data' => $racun], 201);
    }

    public function show(Racun $racuni): JsonResponse
    {
        return response()->json(['data' => $racuni->load(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'uplate'])]);
    }

    public function update(Request $request, Racun $racuni): JsonResponse
    {
        $racuni->update($this->validateData($request));

        return response()->json(['message' => 'Racun je izmenjen.', 'data' => $racuni->fresh()->load(['termin', 'uplate'])]);
    }

    public function destroy(Racun $racuni): JsonResponse
    {
        $racuni->delete();

        return response()->json(['message' => 'Racun je obrisan.']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'termin_id' => ['required', 'exists:termini,id'],
            'datum_izdavanja' => ['required', 'date'],
            'ukupan_iznos' => ['required', 'numeric', 'min:0'],
            'nacin_placanja' => ['nullable', 'string', 'max:255'],
            'status_placanja' => ['required', 'in:placeno,neplaceno,delimicno'],
        ]);
    }
}
