<?php

namespace App\Http\Controllers;

use App\Models\Klijent;
use App\Models\Podsetnik;
use App\Models\Termin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PodsetnikController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $podsetnici = Podsetnik::with(['klijent', 'termin.usluga'])
            ->when($user->isKlijent(), fn ($query) => $query->where('klijent_id', $user->klijent?->id))
            ->latest('datum_slanja')
            ->paginate(15);

        return view('podsetnici.index', compact('podsetnici'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('podsetnici.create', [
            'klijenti' => Klijent::orderBy('prezime')->orderBy('ime')->get(),
            'termini' => Termin::with(['klijent', 'usluga'])->orderByDesc('datum')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        Podsetnik::create($this->validated($request));

        return redirect()->route('podsetnici.index')->with('status', 'Podsetnik je kreiran.');
    }

    public function show(Podsetnik $podsetnici): View
    {
        $this->authorizeAccess($podsetnici);

        $podsetnici->load(['klijent', 'termin.usluga']);

        return view('podsetnici.show', ['podsetnik' => $podsetnici]);
    }

    public function edit(Podsetnik $podsetnici): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('podsetnici.edit', [
            'podsetnik' => $podsetnici,
            'klijenti' => Klijent::orderBy('prezime')->orderBy('ime')->get(),
            'termini' => Termin::with(['klijent', 'usluga'])->orderByDesc('datum')->get(),
        ]);
    }

    public function update(Request $request, Podsetnik $podsetnici): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $podsetnici->update($this->validated($request));

        return redirect()->route('podsetnici.show', $podsetnici)->with('status', 'Podsetnik je sačuvan.');
    }

    public function destroy(Podsetnik $podsetnici): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $podsetnici->delete();

        return redirect()->route('podsetnici.index')->with('status', 'Podsetnik je obrisan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'klijent_id' => ['required', 'exists:klijenti,id'],
            'termin_id' => ['nullable', 'exists:termini,id'],
            'datum_slanja' => ['required', 'date'],
            'tip_podsetnika' => ['required', 'in:SMS,email,aplikacija'],
            'sadrzaj' => ['required', 'string'],
            'status' => ['required', 'in:planiran,poslat,neuspesan'],
        ]);
    }

    private function authorizeAccess(Podsetnik $podsetnik): void
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->klijent?->is($podsetnik->klijent), 403);
    }
}
