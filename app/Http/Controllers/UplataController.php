<?php

namespace App\Http\Controllers;

use App\Models\Racun;
use App\Models\Uplata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UplataController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $uplate = Uplata::with('racun.termin.klijent')->latest('datum_uplate')->paginate(15);

        return view('uplate.index', compact('uplate'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $racuni = Racun::with('termin.klijent')->orderByDesc('datum_izdavanja')->get();

        return view('uplate.create', compact('racuni'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $uplata = Uplata::create($this->validated($request));
        $this->syncRacunStatus($uplata->racun);

        return redirect()->route('uplate.index')->with('status', 'Uplata je evidentirana.');
    }

    public function show(Uplata $uplate): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $uplate->load('racun.termin.klijent');

        return view('uplate.show', ['uplata' => $uplate]);
    }

    public function edit(Uplata $uplate): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $racuni = Racun::with('termin.klijent')->orderByDesc('datum_izdavanja')->get();

        return view('uplate.edit', ['uplata' => $uplate, 'racuni' => $racuni]);
    }

    public function update(Request $request, Uplata $uplate): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $oldRacun = $uplate->racun;
        $uplate->update($this->validated($request));
        $uplate->refresh();
        $this->syncRacunStatus($oldRacun);
        $this->syncRacunStatus($uplate->racun);

        return redirect()->route('uplate.show', $uplate)->with('status', 'Uplata je sačuvana.');
    }

    public function destroy(Uplata $uplate): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $racun = $uplate->racun;
        $uplate->delete();
        $this->syncRacunStatus($racun);

        return redirect()->route('uplate.index')->with('status', 'Uplata je obrisana.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'racun_id' => ['required', 'exists:racuni,id'],
            'datum_uplate' => ['required', 'date'],
            'iznos' => ['required', 'numeric', 'min:0'],
            'status_transakcije' => ['required', 'in:uspesno,na_cekanju,odbijeno'],
        ]);
    }

    private function syncRacunStatus(Racun $racun): void
    {
        $paid = (float) $racun->uplate()->where('status_transakcije', 'uspesno')->sum('iznos');
        $total = (float) $racun->ukupan_iznos;

        $racun->update([
            'status_placanja' => $paid >= $total ? 'placeno' : ($paid > 0 ? 'delimicno' : 'neplaceno'),
        ]);
    }
}
