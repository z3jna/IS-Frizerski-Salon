@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $zaposleni->ime }} {{ $zaposleni->prezime }}</h1>
    @if(auth()->user()->isAdmin())
        <div class="d-flex gap-2">
            <a href="{{ route('zaposleni.edit', $zaposleni) }}" class="btn btn-outline-primary">Izmeni</a>
            <form method="POST" action="{{ route('zaposleni.destroy', $zaposleni) }}" onsubmit="return confirm('Obrisati zaposlenog?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Obriši</button>
            </form>
        </div>
    @endif
</div>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="page-panel p-3">
            <h2 class="h5">Podaci</h2>
            <dl class="mb-0">
                <dt>Email</dt><dd>{{ $zaposleni->user?->email ?? '-' }}</dd>
                <dt>Telefon</dt><dd>{{ $zaposleni->telefon ?? '-' }}</dd>
                <dt>Pozicija</dt><dd>{{ $zaposleni->pozicija ?? '-' }}</dd>
                <dt>Radno vreme</dt><dd>{{ $zaposleni->radno_vreme ?? '-' }}</dd>
                <dt>Datum zaposlenja</dt><dd>{{ $zaposleni->datum_zaposlenja?->format('d.m.Y') ?? '-' }}</dd>
                <dt>Plata</dt><dd>{{ $zaposleni->plata ? number_format($zaposleni->plata, 2) : '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="table-panel p-3">
            <h2 class="h5">Raspored termina</h2>
            <table class="table">
                <thead><tr><th>Datum</th><th>Klijent</th><th>Usluga</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($zaposleni->termini as $termin)
                    <tr>
                        <td>{{ $termin->datum->format('d.m.Y') }} {{ substr($termin->vreme_pocetka, 0, 5) }}</td>
                        <td>{{ $termin->klijent->ime }} {{ $termin->klijent->prezime }}</td>
                        <td>{{ $termin->usluga->naziv }}</td>
                        <td>{{ $termin->status }}</td>
                        <td><a href="{{ route('termini.show', $termin) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Nema termina.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
