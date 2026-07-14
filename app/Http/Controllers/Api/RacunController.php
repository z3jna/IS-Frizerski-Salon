<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Racun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RacunController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Racun::with(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'uplate'])
            ->latest('datum_izdavanja');

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

        $racun = Racun::create($this->validated($request))->load(['termin', 'uplate']);

        return response()->json(['message' => 'Racun je dodat.', 'data' => $racun], 201);
    }

    public function show(Request $request, Racun $racuni): JsonResponse
    {
        $this->authorizeAccess($request, $racuni);

        return response()->json(['data' => $racuni->load(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'uplate'])]);
    }

    public function update(Request $request, Racun $racuni): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isZaposleni(), 403);

        $racuni->update($this->validated($request, $racuni));

        return response()->json(['message' => 'Racun je sacuvan.', 'data' => $racuni->fresh(['termin', 'uplate'])]);
    }

    public function destroy(Request $request, Racun $racuni): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $racuni->delete();

        return response()->json(['message' => 'Racun je obrisan.']);
    }

    private function validated(Request $request, ?Racun $racun = null): array
    {
        return $request->validate([
            'termin_id' => ['required', 'exists:termini,id', Rule::unique('racuni', 'termin_id')->ignore($racun)],
            'datum_izdavanja' => ['required', 'date'],
            'ukupan_iznos' => ['required', 'numeric', 'min:0'],
            'nacin_placanja' => ['nullable', 'string', 'max:255'],
            'status_placanja' => ['required', 'in:placeno,neplaceno,delimicno'],
        ]);
    }

    private function authorizeAccess(Request $request, Racun $racun): void
    {
        $user = $request->user();
        $racun->loadMissing('termin');

        abort_unless(
            $user->isAdmin()
            || ($user->isZaposleni() && $user->zaposleni?->id === $racun->termin->zaposleni_id)
            || ($user->isKlijent() && $user->klijent?->id === $racun->termin->klijent_id),
            403,
        );
    }
}
