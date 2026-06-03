<?php

namespace App\Http\Controllers;

use App\Models\EvidencijaTretmana;
use App\Models\FotografijaTretmana;
use App\Models\Termin;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvidencijaTretmanaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $tretmani = EvidencijaTretmana::with(['termin.klijent', 'termin.zaposleni', 'termin.usluga'])
            ->when($user->isZaposleni(), fn ($query) => $query->whereHas('termin', fn ($sub) => $sub->where('zaposleni_id', $user->zaposleni?->id)))
            ->when($user->isKlijent(), fn ($query) => $query->whereHas('termin', fn ($sub) => $sub->where('klijent_id', $user->klijent?->id)))
            ->latest('datum')
            ->paginate(15);

        return view('tretmani.index', compact('tretmani'));
    }

    public function create(Request $request): View
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isZaposleni(), 403);

        $termini = Termin::with(['klijent', 'usluga'])
            ->whereDoesntHave('evidencijaTretmana')
            ->when(auth()->user()->isZaposleni(), fn ($query) => $query->where('zaposleni_id', auth()->user()->zaposleni?->id))
            ->orderByDesc('datum')
            ->get();

        $selectedTermin = $request->integer('termin_id');

        return view('tretmani.create', compact('termini', 'selectedTermin'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isZaposleni(), 403);

        $data = $this->validated($request);
        $termin = Termin::findOrFail($data['termin_id']);
        $this->authorizeTermin($termin);

        $tretman = EvidencijaTretmana::create($data);
        $termin->update(['status' => 'realizovan']);
        $this->storePhotos($request, $tretman);

        return redirect()->route('tretmani.show', $tretman)->with('status', 'Evidencija tretmana je dodata.');
    }

    public function show(EvidencijaTretmana $tretman): View
    {
        $this->authorizeTretman($tretman);

        $tretman->load(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'fotografije']);

        return view('tretmani.show', compact('tretman'));
    }

    public function edit(EvidencijaTretmana $tretman): View
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isZaposleni(), 403);
        $this->authorizeTretman($tretman);
        $tretman->load('fotografije');

        return view('tretmani.edit', compact('tretman'));
    }

    public function update(Request $request, EvidencijaTretmana $tretman): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isZaposleni(), 403);
        $this->authorizeTretman($tretman);

        $data = $this->validated($request, true);
        unset($data['termin_id']);
        $tretman->update($data);
        $this->storePhotos($request, $tretman);

        return redirect()->route('tretmani.show', $tretman)->with('status', 'Evidencija tretmana je sačuvana.');
    }

    public function destroy(EvidencijaTretmana $tretman): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $tretman->delete();

        return redirect()->route('tretmani.index')->with('status', 'Evidencija tretmana je obrisana.');
    }

    public function photo(EvidencijaTretmana $tretman, FotografijaTretmana $fotografija): StreamedResponse
    {
        $this->authorizeTretman($tretman);
        abort_unless($fotografija->evidencija_tretmana_id === $tretman->id, 404);
        abort_unless(Storage::disk('public')->exists($fotografija->putanja), 404);

        return Storage::disk('public')->response($fotografija->putanja, $fotografija->naziv);
    }

    private function validated(Request $request, bool $editing = false): array
    {
        return $request->validate([
            'termin_id' => [$editing ? 'nullable' : 'required', 'exists:termini,id', 'unique:evidencija_tretmana,termin_id'],
            'datum' => ['required', 'date'],
            'opis_tretmana' => ['required', 'string'],
            'nijansa' => ['nullable', 'string', 'max:255'],
            'proizvodjac' => ['nullable', 'string', 'max:255'],
            'formula' => ['nullable', 'string'],
            'korisceni_preparati' => ['nullable', 'string'],
            'napomena' => ['nullable', 'string'],
            'fotografije_pre.*' => ['nullable', 'image', 'max:4096'],
            'fotografije_posle.*' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function storePhotos(Request $request, EvidencijaTretmana $tretman): void
    {
        foreach (['fotografije_pre' => 'pre', 'fotografije_posle' => 'posle'] as $field => $type) {
            foreach ($request->file($field, []) as $file) {
                $path = $file->store('tretmani', 'public');

                $tretman->fotografije()->create([
                    'naziv' => $file->getClientOriginalName(),
                    'putanja' => $path,
                    'tip_fotografije' => $type,
                    'datum_dodavanja' => Carbon::now(),
                    'opis' => null,
                ]);
            }
        }
    }

    private function authorizeTermin(Termin $termin): void
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->zaposleni?->is($termin->zaposleni), 403);
    }

    private function authorizeTretman(EvidencijaTretmana $tretman): void
    {
        $termin = $tretman->termin;
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || ($user->isZaposleni() && $user->zaposleni?->is($termin->zaposleni))
            || ($user->isKlijent() && $user->klijent?->is($termin->klijent));

        abort_unless($allowed, 403);
    }
}
