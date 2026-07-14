<?php

namespace App\Http\Controllers;

use App\Models\Racun;
use App\Models\Termin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RacunController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $racuni = Racun::with(['termin.klijent', 'termin.usluga', 'uplate'])
            ->when($user->isKlijent(), fn ($query) => $query->whereHas('termin', fn ($sub) => $sub->where('klijent_id', $user->klijent?->id)))
            ->latest('datum_izdavanja')
            ->paginate(15);

        return view('racuni.index', compact('racuni'));
    }

    public function create(Request $request): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $termini = Termin::with(['klijent', 'usluga'])
            ->where('status', 'realizovan')
            ->whereDoesntHave('racun')
            ->orderByDesc('datum')
            ->get();
        $selectedTermin = $request->integer('termin_id');

        return view('racuni.create', compact('termini', 'selectedTermin'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $this->validated($request);

        Racun::create($data);

        return redirect()->route('racuni.index')->with('status', 'Račun je generisan.');
    }

    public function show(Racun $racuni): View
    {
        $this->authorizeAccess($racuni);

        $racuni->load(['termin.klijent', 'termin.zaposleni', 'termin.usluga', 'uplate']);

        return view('racuni.show', ['racun' => $racuni]);
    }

    public function edit(Racun $racuni): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('racuni.edit', ['racun' => $racuni]);
    }

    public function update(Request $request, Racun $racuni): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $racuni->update($request->validate([
            'datum_izdavanja' => ['required', 'date'],
            'ukupan_iznos' => ['required', 'numeric', 'min:0'],
            'nacin_placanja' => ['nullable', 'string', 'max:255'],
            'status_placanja' => ['required', 'in:neplaceno,delimicno,placeno'],
        ]));

        return redirect()->route('racuni.show', $racuni)->with('status', 'Račun je sačuvan.');
    }

    public function destroy(Racun $racuni): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $racuni->delete();

        return redirect()->route('racuni.index')->with('status', 'Račun je obrisan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'termin_id' => [
                'required',
                Rule::exists('termini', 'id')->where('status', 'realizovan'),
                'unique:racuni,termin_id',
            ],
            'datum_izdavanja' => ['required', 'date'],
            'ukupan_iznos' => ['required', 'numeric', 'min:0'],
            'nacin_placanja' => ['nullable', 'string', 'max:255'],
            'status_placanja' => ['required', 'in:neplaceno,delimicno,placeno'],
        ]);
    }

    private function authorizeAccess(Racun $racun): void
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->klijent?->is($racun->termin->klijent), 403);
    }
}
