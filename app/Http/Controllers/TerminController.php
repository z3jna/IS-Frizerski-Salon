<?php

namespace App\Http\Controllers;

use App\Models\Klijent;
use App\Models\Termin;
use App\Models\User;
use App\Models\Usluga;
use App\Models\Zaposleni;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TerminController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $termini = Termin::with(['klijent', 'zaposleni', 'usluga'])
            ->when($user->role === User::ROLE_KLIJENT, fn ($query) => $query->where('klijent_id', $user->klijent?->id))
            ->when($user->role === User::ROLE_ZAPOSLENI, fn ($query) => $query->where('zaposleni_id', $user->zaposleni?->id))
            ->orderByDesc('datum')
            ->orderBy('vreme_pocetka')
            ->paginate(15);

        return view('termini.index', compact('termini'));
    }

    public function create(): View
    {
        $klijenti = Klijent::orderBy('prezime')->orderBy('ime')->get();
        $zaposleni = Zaposleni::orderBy('prezime')->orderBy('ime')->get();
        $usluge = Usluga::where('dostupnost', true)->orderBy('naziv')->get();

        return view('termini.create', compact('klijenti', 'zaposleni', 'usluge'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = auth()->user();

        if ($user->isKlijent()) {
            abort_unless($user->klijent, 403, 'Korisnik nema povezan profil klijenta.');
            $data['klijent_id'] = $user->klijent->id;
        }

        $data['vreme_zavrsetka'] = $this->calculateEndTime($data['datum'], $data['vreme_pocetka'], (int) Usluga::findOrFail($data['usluga_id'])->trajanje_minuta);
        $this->ensureNoOverlap($data);

        Termin::create($data);

        return redirect()->route('termini.index')->with('status', 'Termin je zakazan.');
    }

    public function show(Termin $termini): View
    {
        $this->authorizeAccess($termini);

        $termini->load(['klijent', 'zaposleni', 'usluga', 'evidencijaTretmana.fotografije', 'racun.uplate']);

        return view('termini.show', ['termin' => $termini]);
    }

    public function edit(Termin $termini): View
    {
        $this->authorizeAccess($termini, true);

        $klijenti = Klijent::orderBy('prezime')->orderBy('ime')->get();
        $zaposleni = Zaposleni::orderBy('prezime')->orderBy('ime')->get();
        $usluge = Usluga::orderBy('naziv')->get();

        return view('termini.edit', ['termin' => $termini, 'klijenti' => $klijenti, 'zaposleni' => $zaposleni, 'usluge' => $usluge]);
    }

    public function update(Request $request, Termin $termini): RedirectResponse
    {
        $this->authorizeAccess($termini, true);

        $data = $this->validated($request, true);
        $data['vreme_zavrsetka'] = $this->calculateEndTime($data['datum'], $data['vreme_pocetka'], (int) Usluga::findOrFail($data['usluga_id'])->trajanje_minuta);
        $this->ensureNoOverlap($data, $termini);

        $termini->update($data);

        return redirect()->route('termini.show', $termini)->with('status', 'Termin je sačuvan.');
    }

    public function destroy(Termin $termini): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $termini->delete();

        return redirect()->route('termini.index')->with('status', 'Termin je obrisan.');
    }

    public function cancel(Termin $termin): RedirectResponse
    {
        $this->authorizeAccess($termin);

        abort_if($termin->status === 'realizovan', 422, 'Realizovan termin ne može biti otkazan.');

        $termin->update(['status' => 'otkazan']);

        return redirect()->route('termini.index')->with('status', 'Termin je otkazan.');
    }

    private function validated(Request $request, bool $includeStatus = false): array
    {
        $rules = [
            'datum' => ['required', 'date'],
            'vreme_pocetka' => ['required', 'date_format:H:i'],
            'napomena' => ['nullable', 'string'],
            'zaposleni_id' => ['required', 'exists:zaposleni,id'],
            'usluga_id' => ['required', 'exists:usluge,id'],
        ];

        if (! auth()->user()->isKlijent()) {
            $rules['klijent_id'] = ['required', 'exists:klijenti,id'];
        }

        if ($includeStatus) {
            $rules['status'] = ['required', 'in:zakazan,realizovan,otkazan'];
        }

        return $request->validate($rules);
    }

    private function calculateEndTime(string $date, string $start, int $durationMinutes): string
    {
        return Carbon::parse($date.' '.$start)->addMinutes($durationMinutes)->format('H:i:s');
    }

    private function ensureNoOverlap(array $data, ?Termin $ignore = null): void
    {
        $overlap = Termin::where('zaposleni_id', $data['zaposleni_id'])
            ->where('datum', $data['datum'])
            ->where('status', '!=', 'otkazan')
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->where('vreme_pocetka', '<', $data['vreme_zavrsetka'])
            ->where('vreme_zavrsetka', '>', $data['vreme_pocetka'])
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'vreme_pocetka' => 'Izabrani zaposleni već ima termin u tom periodu.',
            ]);
        }
    }

    private function authorizeAccess(Termin $termin, bool $editing = false): void
    {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || ($user->isZaposleni() && $user->zaposleni?->is($termin->zaposleni))
            || (! $editing && $user->isKlijent() && $user->klijent?->is($termin->klijent));

        abort_unless($allowed, 403);
    }
}
