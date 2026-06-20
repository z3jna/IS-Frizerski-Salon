<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Termin;
use App\Models\User;
use App\Models\Usluga;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TerminController extends Controller
{
    private const WORK_START = '08:00';
    private const WORK_END = '20:00';

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $termini = Termin::with(['klijent.user', 'zaposleni.user', 'usluga'])
            ->when($user->isKlijent(), fn ($query) => $query->where('klijent_id', $user->klijent?->id))
            ->when($user->isZaposleni(), fn ($query) => $query->where('zaposleni_id', $user->zaposleni?->id))
            ->orderByDesc('datum')
            ->orderBy('vreme_pocetka')
            ->get();

        return response()->json(['data' => $termini]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedForCreate($request);

        if ($request->user()->isKlijent()) {
            abort_unless($request->user()->klijent, 403, 'Korisnik nema povezan profil klijenta.');
            $data['klijent_id'] = $request->user()->klijent->id;
        }

        $data['status'] = $data['status'] ?? 'zakazan';
        $data['vreme_zavrsetka'] = $this->calculateEndTime($data['datum'], $data['vreme_pocetka'], $data['usluga_id']);
        $this->ensureBookableTime($data);
        $this->ensureNoOverlap($data);

        $termin = Termin::create($data)->load(['klijent', 'zaposleni', 'usluga']);

        return response()->json(['message' => 'Termin je zakazan.', 'data' => $termin], 201);
    }

    public function show(Request $request, Termin $termini): JsonResponse
    {
        $this->authorizeAccess($request, $termini);

        return response()->json([
            'data' => $termini->load(['klijent.user', 'zaposleni.user', 'usluga', 'evidencijaTretmana.fotografije', 'racun.uplate']),
        ]);
    }

    public function update(Request $request, Termin $termini): JsonResponse
    {
        $this->authorizeAccess($request, $termini, true);

        $data = $this->validatedForUpdate($request);
        $merged = array_merge($termini->only([
            'datum',
            'vreme_pocetka',
            'klijent_id',
            'zaposleni_id',
            'usluga_id',
            'status',
            'napomena',
        ]), $data);

        $merged['datum'] = Carbon::parse($merged['datum'])->format('Y-m-d');
        $merged['vreme_zavrsetka'] = $this->calculateEndTime($merged['datum'], substr($merged['vreme_pocetka'], 0, 5), (int) $merged['usluga_id']);
        $this->ensureBookableTime($merged);
        $this->ensureNoOverlap($merged, $termini);

        $termini->update($merged);

        return response()->json(['message' => 'Termin je sacuvan.', 'data' => $termini->fresh(['klijent', 'zaposleni', 'usluga'])]);
    }

    public function destroy(Request $request, Termin $termini): JsonResponse
    {
        $this->authorizeAccess($request, $termini);

        if ($request->user()->isAdmin()) {
            $termini->delete();

            return response()->json(['message' => 'Termin je obrisan.']);
        }

        abort_if($termini->status === 'realizovan', 422, 'Realizovan termin ne moze biti otkazan.');
        $termini->update(['status' => 'otkazan']);

        return response()->json(['message' => 'Termin je otkazan.', 'data' => $termini->fresh()]);
    }

    public function forKlijent(Request $request, int $klijentId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isZaposleni() || $user->klijent?->id === $klijentId, 403);

        return response()->json([
            'data' => Termin::with(['zaposleni', 'usluga'])
                ->where('klijent_id', $klijentId)
                ->orderByDesc('datum')
                ->orderBy('vreme_pocetka')
                ->get(),
        ]);
    }

    public function forZaposleni(Request $request, int $zaposleniId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isKlijent() || $user->zaposleni?->id === $zaposleniId, 403);

        return response()->json([
            'data' => Termin::with(['klijent', 'usluga'])
                ->where('zaposleni_id', $zaposleniId)
                ->orderByDesc('datum')
                ->orderBy('vreme_pocetka')
                ->get(),
        ]);
    }

    public function dostupni(Request $request): JsonResponse
    {
        $data = $request->validate([
            'datum' => ['required', 'date', 'after_or_equal:today'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id'],
        ]);

        $duration = (int) Usluga::findOrFail($data['usluga_id'])->trajanje_minuta;
        $date = Carbon::parse($data['datum'])->format('Y-m-d');
        $workStart = Carbon::parse($date.' '.self::WORK_START);
        $workEnd = Carbon::parse($date.' '.self::WORK_END);
        $now = now();
        $slots = [];

        for ($start = $workStart->copy(); $start->copy()->addMinutes($duration)->lte($workEnd); $start->addMinutes(30)) {
            if ($start->lte($now)) {
                continue;
            }

            $candidate = [
                'datum' => $date,
                'vreme_pocetka' => $start->format('H:i'),
                'vreme_zavrsetka' => $start->copy()->addMinutes($duration)->format('H:i'),
                'zaposleni_id' => $data['zaposleni_id'],
            ];

            if (! $this->hasOverlap($candidate)) {
                $slots[] = [
                    'datum' => $candidate['datum'],
                    'vreme_pocetka' => $candidate['vreme_pocetka'],
                    'vreme_zavrsetka' => $candidate['vreme_zavrsetka'],
                ];
            }
        }

        return response()->json(['data' => $slots]);
    }

    private function validatedForCreate(Request $request): array
    {
        $rules = [
            'datum' => ['required', 'date', 'after_or_equal:today'],
            'vreme_pocetka' => ['required', 'date_format:H:i'],
            'status' => ['nullable', 'in:zakazan,realizovan,otkazan'],
            'napomena' => ['nullable', 'string'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id'],
        ];

        if (! $request->user()->isKlijent()) {
            $rules['klijent_id'] = ['required', 'exists:klijenti,id'];
        }

        return $request->validate($rules);
    }

    private function validatedForUpdate(Request $request): array
    {
        return $request->validate([
            'datum' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
            'vreme_pocetka' => ['sometimes', 'required', 'date_format:H:i'],
            'status' => ['sometimes', 'required', 'in:zakazan,realizovan,otkazan'],
            'napomena' => ['nullable', 'string'],
            'klijent_id' => ['sometimes', 'required', 'exists:klijenti,id'],
            'zaposleni_id' => ['sometimes', 'required', 'exists:zaposleni,id'],
            'usluga_id' => ['sometimes', 'required', 'exists:usluge,id'],
        ]);
    }

    private function calculateEndTime(string $date, string $start, int $uslugaId): string
    {
        $duration = (int) Usluga::findOrFail($uslugaId)->trajanje_minuta;

        return Carbon::parse($date.' '.$start)->addMinutes($duration)->format('H:i:s');
    }

    private function ensureNoOverlap(array $data, ?Termin $ignore = null): void
    {
        if ($this->hasOverlap($data, $ignore)) {
            throw ValidationException::withMessages([
                'vreme_pocetka' => 'Izabrani zaposleni vec ima termin u tom periodu.',
            ]);
        }
    }

    private function ensureBookableTime(array $data): void
    {
        $start = Carbon::parse($data['datum'].' '.$data['vreme_pocetka']);
        $end = Carbon::parse($data['datum'].' '.$data['vreme_zavrsetka']);
        $workStart = Carbon::parse($data['datum'].' '.self::WORK_START);
        $workEnd = Carbon::parse($data['datum'].' '.self::WORK_END);

        if ($start->lte(now())) {
            throw ValidationException::withMessages([
                'vreme_pocetka' => 'Termin mora biti zakazan u buducnosti. Nije moguce zakazati datum ili vreme koje je vec proslo.',
            ]);
        }

        if ($start->lt($workStart) || $end->gt($workEnd)) {
            throw ValidationException::withMessages([
                'vreme_pocetka' => 'Termin mora biti u okviru radnog vremena od 08:00 do 20:00.',
            ]);
        }
    }

    private function hasOverlap(array $data, ?Termin $ignore = null): bool
    {
        return Termin::where('zaposleni_id', $data['zaposleni_id'])
            ->whereDate('datum', $data['datum'])
            ->where('status', '!=', 'otkazan')
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->where('vreme_pocetka', '<', $data['vreme_zavrsetka'])
            ->where('vreme_zavrsetka', '>', $data['vreme_pocetka'])
            ->exists();
    }

    private function authorizeAccess(Request $request, Termin $termin, bool $editing = false): void
    {
        $user = $request->user();
        $clientCancelsOwnTermin = $editing
            && $request->input('status') === 'otkazan'
            && count(array_diff(array_keys($request->all()), ['status'])) === 0
            && $user->role === User::ROLE_KLIJENT
            && $user->klijent?->is($termin->klijent);

        $allowed = $user->role === User::ROLE_ADMIN
            || ($user->role === User::ROLE_ZAPOSLENI && $user->zaposleni?->is($termin->zaposleni))
            || (! $editing && $user->role === User::ROLE_KLIJENT && $user->klijent?->is($termin->klijent))
            || $clientCancelsOwnTermin;

        abort_unless($allowed, 403);
    }
}
