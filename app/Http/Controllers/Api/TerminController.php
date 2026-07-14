<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Termin;
use App\Models\Usluga;
use App\Models\Zaposleni;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TerminController extends Controller
{
    private const WORK_START = '08:00';

    private const WORK_END = '20:00';

    public function options(): JsonResponse
    {
        return response()->json([
            'data' => [
                'usluge' => Usluga::where('dostupnost', true)
                    ->orderBy('tip_usluge')
                    ->orderBy('naziv')
                    ->get(),
                'zaposleni' => Zaposleni::orderBy('prezime')
                    ->orderBy('ime')
                    ->get(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isKlijent(), 403, 'Samo klijent moze da zakaze termin.');
        abort_unless($request->user()->klijent, 403, 'Korisnik nema povezan profil klijenta.');

        $data = $this->validatedForCreate($request);
        $data['klijent_id'] = $request->user()->klijent->id;
        $data['status'] = 'zakazan';
        $data['vreme_zavrsetka'] = $this->calculateEndTime($data['datum'], $data['vreme_pocetka'], $data['usluga_id']);
        $this->ensureBookableTime($data);
        $this->ensureNoOverlap($data);

        $termin = Termin::create($data)->load(['klijent', 'zaposleni', 'usluga']);

        return response()->json(['message' => 'Termin je zakazan.', 'data' => $termin], 201);
    }

    public function dostupni(Request $request): JsonResponse
    {
        $data = $request->validate([
            'datum' => ['required', 'date', 'after_or_equal:today'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id,dostupnost,1'],
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
        return $request->validate([
            'datum' => ['required', 'date', 'after_or_equal:today'],
            'vreme_pocetka' => ['required', 'date_format:H:i'],
            'napomena' => ['nullable', 'string'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id,dostupnost,1'],
        ]);
    }

    private function calculateEndTime(string $date, string $start, int $uslugaId): string
    {
        $duration = (int) Usluga::findOrFail($uslugaId)->trajanje_minuta;

        return Carbon::parse($date.' '.$start)->addMinutes($duration)->format('H:i:s');
    }

    private function ensureNoOverlap(array $data): void
    {
        if ($this->hasOverlap($data)) {
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

    private function hasOverlap(array $data): bool
    {
        return Termin::where('zaposleni_id', $data['zaposleni_id'])
            ->whereDate('datum', $data['datum'])
            ->where('status', '!=', 'otkazan')
            ->where('vreme_pocetka', '<', $data['vreme_zavrsetka'])
            ->where('vreme_zavrsetka', '>', $data['vreme_pocetka'])
            ->exists();
    }
}
