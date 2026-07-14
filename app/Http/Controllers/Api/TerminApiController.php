<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Termin;
use App\Models\User;
use App\Models\Usluga;
use App\Models\Zaposleni;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TerminApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $termini = $this->baseQueryForUser($request->user())
            ->orderByDesc('datum')
            ->orderBy('vreme_pocetka')
            ->get();

        return response()->json(['data' => $termini]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateForStore($request);

        if ($request->user()->isKlijent()) {
            abort_unless($request->user()->klijent, 403, 'Korisnik nema povezan profil klijenta.');
            $data['klijent_id'] = $request->user()->klijent->id;
        }

        $data = $this->normalizeEndTime($data);
        $this->ensureNoOverlap($data);

        $termin = Termin::create($data)->load(['klijent', 'zaposleni', 'usluga']);

        return response()->json(['message' => 'Termin je zakazan.', 'data' => $termin], 201);
    }

    public function show(Request $request, Termin $termin): JsonResponse
    {
        $this->authorizeTermin($request->user(), $termin);

        return response()->json([
            'data' => $termin->load(['klijent', 'zaposleni', 'usluga', 'evidencijaTretmana.fotografije', 'racun.uplate']),
        ]);
    }

    public function update(Request $request, Termin $termin): JsonResponse
    {
        $this->authorizeTermin($request->user(), $termin, true);

        $data = $this->validateForUpdate($request, $termin);
        $data = $this->normalizeEndTime($data);
        $this->ensureNoOverlap($data, $termin);

        $termin->update($data);

        return response()->json([
            'message' => 'Termin je izmenjen.',
            'data' => $termin->fresh()->load(['klijent', 'zaposleni', 'usluga']),
        ]);
    }

    public function destroy(Request $request, Termin $termin): JsonResponse
    {
        $this->authorizeTermin($request->user(), $termin, true);
        $termin->delete();

        return response()->json(['message' => 'Termin je obrisan.']);
    }

    public function forKlijent(Request $request, int $klijentId): JsonResponse
    {
        abort_if($request->user()->isKlijent() && $request->user()->klijent?->id !== $klijentId, 403);

        $termini = Termin::with(['klijent', 'zaposleni', 'usluga'])
            ->where('klijent_id', $klijentId)
            ->orderByDesc('datum')
            ->orderBy('vreme_pocetka')
            ->get();

        return response()->json(['data' => $termini]);
    }

    public function forZaposleni(Request $request, int $zaposleniId): JsonResponse
    {
        abort_if($request->user()->isZaposleni() && $request->user()->zaposleni?->id !== $zaposleniId, 403);

        $termini = Termin::with(['klijent', 'zaposleni', 'usluga'])
            ->where('zaposleni_id', $zaposleniId)
            ->orderByDesc('datum')
            ->orderBy('vreme_pocetka')
            ->get();

        return response()->json(['data' => $termini]);
    }

    public function dostupni(Request $request): JsonResponse
    {
        $data = $request->validate([
            'datum' => ['required', 'date', 'after_or_equal:today'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id'],
        ]);

        $usluga = Usluga::findOrFail($data['usluga_id']);
        $slots = [];
        $start = Carbon::parse($data['datum'].' 09:00');
        $lastStart = Carbon::parse($data['datum'].' 18:00')->subMinutes($usluga->trajanje_minuta);

        while ($start->lte($lastStart)) {
            $end = $start->copy()->addMinutes($usluga->trajanje_minuta);
            $candidate = [
                'datum' => $data['datum'],
                'zaposleni_id' => $data['zaposleni_id'],
                'vreme_pocetka' => $start->format('H:i:s'),
                'vreme_zavrsetka' => $end->format('H:i:s'),
            ];

            if (! $this->hasOverlap($candidate)) {
                $slots[] = [
                    'datum' => $data['datum'],
                    'vreme_pocetka' => $start->format('H:i'),
                    'vreme_zavrsetka' => $end->format('H:i'),
                ];
            }

            $start->addMinutes(30);
        }

        return response()->json([
            'zaposleni' => Zaposleni::find($data['zaposleni_id']),
            'usluga' => $usluga,
            'data' => $slots,
        ]);
    }

    private function baseQueryForUser(User $user)
    {
        return Termin::with(['klijent', 'zaposleni', 'usluga'])
            ->when($user->isKlijent(), fn ($query) => $query->where('klijent_id', $user->klijent?->id))
            ->when($user->isZaposleni(), fn ($query) => $query->where('zaposleni_id', $user->zaposleni?->id));
    }

    private function validateForStore(Request $request): array
    {
        $rules = [
            'datum' => ['required', 'date', 'after_or_equal:today'],
            'vreme_pocetka' => ['required', 'date_format:H:i'],
            'vreme_zavrsetka' => ['nullable', 'date_format:H:i'],
            'napomena' => ['nullable', 'string'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id'],
            'status' => ['sometimes', 'in:zakazan,realizovan,otkazan'],
        ];

        if (! $request->user()->isKlijent()) {
            $rules['klijent_id'] = ['required', 'exists:klijenti,id'];
        }

        $data = $request->validate($rules);
        $data['status'] = $data['status'] ?? 'zakazan';

        return $data;
    }

    private function validateForUpdate(Request $request, Termin $termin): array
    {
        $data = $request->validate([
            'datum' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
            'vreme_pocetka' => ['sometimes', 'required', 'date_format:H:i'],
            'vreme_zavrsetka' => ['nullable', 'date_format:H:i'],
            'napomena' => ['nullable', 'string'],
            'klijent_id' => ['sometimes', 'required', 'exists:klijenti,id'],
            'zaposleni_id' => ['sometimes', 'required', 'exists:zaposleni,id'],
            'usluga_id' => ['sometimes', 'required', 'exists:usluge,id'],
            'status' => ['sometimes', 'required', 'in:zakazan,realizovan,otkazan'],
        ]);

        return array_merge($termin->only([
            'datum',
            'vreme_pocetka',
            'vreme_zavrsetka',
            'status',
            'napomena',
            'klijent_id',
            'zaposleni_id',
            'usluga_id',
        ]), $data);
    }

    private function normalizeEndTime(array $data): array
    {
        $start = Carbon::parse($data['datum'].' '.$data['vreme_pocetka']);

        if (empty($data['vreme_zavrsetka'])) {
            $duration = (int) Usluga::findOrFail($data['usluga_id'])->trajanje_minuta;
            $data['vreme_zavrsetka'] = $start->copy()->addMinutes($duration)->format('H:i:s');
        }

        $end = Carbon::parse($data['datum'].' '.$data['vreme_zavrsetka']);

        if ($end->lte($start)) {
            throw ValidationException::withMessages([
                'vreme_zavrsetka' => 'Vreme zavrsetka mora biti posle vremena pocetka.',
            ]);
        }

        $data['vreme_pocetka'] = $start->format('H:i:s');
        $data['vreme_zavrsetka'] = $end->format('H:i:s');

        return $data;
    }

    private function ensureNoOverlap(array $data, ?Termin $ignore = null): void
    {
        if ($this->hasOverlap($data, $ignore)) {
            throw ValidationException::withMessages([
                'vreme_pocetka' => 'Izabrani zaposleni vec ima termin u tom periodu.',
            ]);
        }
    }

    private function hasOverlap(array $data, ?Termin $ignore = null): bool
    {
        return Termin::where('zaposleni_id', $data['zaposleni_id'])
            ->where('datum', $data['datum'])
            ->where('status', '!=', 'otkazan')
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->where('vreme_pocetka', '<', $data['vreme_zavrsetka'])
            ->where('vreme_zavrsetka', '>', $data['vreme_pocetka'])
            ->exists();
    }

    private function authorizeTermin(User $user, Termin $termin, bool $editing = false): void
    {
        $allowed = $user->isAdmin()
            || ($user->isZaposleni() && $user->zaposleni?->is($termin->zaposleni))
            || (! $editing && $user->isKlijent() && $user->klijent?->is($termin->klijent))
            || ($editing && $user->isKlijent() && $user->klijent?->is($termin->klijent) && request('status') === 'otkazan');

        abort_unless($allowed, 403);
    }
}
