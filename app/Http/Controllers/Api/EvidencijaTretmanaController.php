<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvidencijaTretmana;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EvidencijaTretmanaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = EvidencijaTretmana::with(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'fotografije'])
            ->latest('datum');

        if ($request->user()->isKlijent()) {
            $query->whereHas('termin', fn ($q) => $q->where('klijent_id', $request->user()->klijent?->id));
        }

        if ($request->user()->isZaposleni()) {
            $query->whereHas('termin', fn ($q) => $q->where('zaposleni_id', $request->user()->zaposleni?->id));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isZaposleni(), 403);

        $tretman = EvidencijaTretmana::create($this->validated($request))->load(['termin', 'fotografije']);

        return response()->json(['message' => 'Evidencija tretmana je dodata.', 'data' => $tretman], 201);
    }

    public function show(Request $request, EvidencijaTretmana $tretmani): JsonResponse
    {
        $this->authorizeAccess($request, $tretmani);

        return response()->json(['data' => $tretmani->load(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'fotografije'])]);
    }

    public function update(Request $request, EvidencijaTretmana $tretmani): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isZaposleni(), 403);

        $tretmani->update($this->validated($request, $tretmani));

        return response()->json(['message' => 'Evidencija tretmana je sacuvana.', 'data' => $tretmani->fresh(['termin', 'fotografije'])]);
    }

    public function destroy(Request $request, EvidencijaTretmana $tretmani): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $tretmani->delete();

        return response()->json(['message' => 'Evidencija tretmana je obrisana.']);
    }

    private function validated(Request $request, ?EvidencijaTretmana $tretman = null): array
    {
        return $request->validate([
            'termin_id' => ['required', 'exists:termini,id', Rule::unique('evidencija_tretmana', 'termin_id')->ignore($tretman)],
            'datum' => ['required', 'date'],
            'opis_tretmana' => ['required', 'string'],
            'nijansa' => ['nullable', 'string', 'max:255'],
            'proizvodjac' => ['nullable', 'string', 'max:255'],
            'formula' => ['nullable', 'string'],
            'korisceni_preparati' => ['nullable', 'string'],
            'napomena' => ['nullable', 'string'],
        ]);
    }

    private function authorizeAccess(Request $request, EvidencijaTretmana $tretman): void
    {
        $user = $request->user();
        $tretman->loadMissing('termin');

        abort_unless(
            $user->isAdmin()
            || ($user->isZaposleni() && $user->zaposleni?->id === $tretman->termin->zaposleni_id)
            || ($user->isKlijent() && $user->klijent?->id === $tretman->termin->klijent_id),
            403,
        );
    }
}
