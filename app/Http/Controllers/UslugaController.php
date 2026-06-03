<?php

namespace App\Http\Controllers;

use App\Models\Usluga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UslugaController extends Controller
{
    public function index(): View
    {
        $usluge = Usluga::orderBy('tip_usluge')->orderBy('naziv')->paginate(15);

        return view('usluge.index', compact('usluge'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('usluge.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        Usluga::create($this->validated($request));

        return redirect()->route('usluge.index')->with('status', 'Usluga je dodata.');
    }

    public function show(Usluga $usluge): View
    {
        return view('usluge.show', ['usluga' => $usluge]);
    }

    public function edit(Usluga $usluge): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('usluge.edit', ['usluga' => $usluge]);
    }

    public function update(Request $request, Usluga $usluge): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $usluge->update($this->validated($request));

        return redirect()->route('usluge.show', $usluge)->with('status', 'Usluga je sačuvana.');
    }

    public function destroy(Usluga $usluge): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $usluge->delete();

        return redirect()->route('usluge.index')->with('status', 'Usluga je obrisana.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'naziv' => ['required', 'string', 'max:255'],
            'tip_usluge' => ['required', 'string', 'max:255'],
            'opis' => ['nullable', 'string'],
            'trajanje_minuta' => ['required', 'integer', 'min:10', 'max:600'],
            'cena' => ['required', 'numeric', 'min:0'],
            'dostupnost' => ['nullable', 'boolean'],
        ]) + ['dostupnost' => false];
    }
}
