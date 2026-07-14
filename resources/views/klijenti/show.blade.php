@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $klijent->ime }} {{ $klijent->prezime }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('klijenti.edit', $klijent) }}" class="btn btn-outline-primary">Izmeni</a>
        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('klijenti.destroy', $klijent) }}" onsubmit="return confirm('Obrisati klijenta?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Obriši</button>
            </form>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="page-panel p-3">
            <h2 class="h5">Podaci</h2>
            <dl class="mb-0">
                <dt>Email</dt><dd>{{ $klijent->user?->email ?? '-' }}</dd>
                <dt>Telefon</dt><dd>{{ $klijent->telefon ?? '-' }}</dd>
                <dt>Adresa</dt><dd>{{ $klijent->adresa ?? '-' }}</dd>
                <dt>Datum rođenja</dt><dd>{{ $klijent->datum_rodjenja?->format('d.m.Y') ?? '-' }}</dd>
                <dt>Preferencije</dt><dd>{{ $klijent->preferencije ?? '-' }}</dd>
                <dt>Napomena</dt><dd>{{ $klijent->napomena ?? '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="table-panel p-3 mb-3">
            <h2 class="h5">Istorija termina</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Datum</th><th>Usluga</th><th>Zaposleni</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($klijent->termini as $termin)
                        <tr>
                            <td>{{ $termin->datum->format('d.m.Y') }} {{ substr($termin->vreme_pocetka, 0, 5) }}</td>
                            <td>{{ $termin->usluga->naziv }}</td>
                            <td>{{ $termin->zaposleni->ime }} {{ $termin->zaposleni->prezime }}</td>
                            <td>{{ $termin->status }}</td>
                            <td><a href="{{ route('termini.show', $termin) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Nema istorije.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="page-panel p-3">
            <h2 class="h5">Fotografije pre/posle</h2>
            @include('tretmani._photo_gallery', [
                'photos' => $klijent->termini->flatMap(fn($termin) => $termin->evidencijaTretmana?->fotografije ?? collect()),
            ])
        </div>
    </div>
</div>
@endsection
