@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Termini</h1>
    <a href="/termini/create" class="btn btn-primary">Novi termin</a>
</div>
<div class="table-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Datum</th><th>Klijent</th><th>Zaposleni</th><th>Usluga</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($termini as $termin)
                <tr>
                    <td>{{ $termin->datum->format('d.m.Y') }} {{ substr($termin->vreme_pocetka, 0, 5) }}-{{ substr($termin->vreme_zavrsetka, 0, 5) }}</td>
                    <td>{{ $termin->klijent->ime }} {{ $termin->klijent->prezime }}</td>
                    <td>{{ $termin->zaposleni->ime }} {{ $termin->zaposleni->prezime }}</td>
                    <td>{{ $termin->usluga->naziv }}</td>
                    <td><span class="badge text-bg-secondary badge-status">{{ ['zakazan' => 'Zakazan', 'realizovan' => 'Realizovan', 'otkazan' => 'Otkazan'][$termin->status] ?? $termin->status }}</span></td>
                    <td class="text-end"><a href="{{ route('termini.show', $termin) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">Nema termina.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $termini->links() }}
</div>
@endsection
