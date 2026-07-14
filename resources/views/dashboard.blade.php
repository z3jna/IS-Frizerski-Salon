@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Pregled rada za ulogu: {{ $user->role }}</p>
    </div>
    <a href="/termini/create" class="btn btn-primary">Novi termin</a>
</div>

@if($user->isAdmin())
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="metric-card"><div class="label">Zakazani termini</div><div class="value">{{ $stats['zakazani'] }}</div></div></div>
        <div class="col-md-3"><div class="metric-card"><div class="label">Realizovani termini</div><div class="value">{{ $stats['realizovani'] }}</div></div></div>
        <div class="col-md-3"><div class="metric-card"><div class="label">Otkazani termini</div><div class="value">{{ $stats['otkazani'] }}</div></div></div>
        <div class="col-md-3"><div class="metric-card"><div class="label">Prihodi</div><div class="value">{{ number_format($stats['prihodi'], 2) }}</div></div></div>
    </div>
@endif

<div class="table-panel p-3">
    <h2 class="h5 mb-3">Najbliži termini</h2>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Datum</th>
                <th>Vreme</th>
                <th>Klijent</th>
                <th>Zaposleni</th>
                <th>Usluga</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($termini as $termin)
                <tr>
                    <td>{{ $termin->datum->format('d.m.Y') }}</td>
                    <td>{{ substr($termin->vreme_pocetka, 0, 5) }} - {{ substr($termin->vreme_zavrsetka, 0, 5) }}</td>
                    <td>{{ $termin->klijent->ime }} {{ $termin->klijent->prezime }}</td>
                    <td>{{ $termin->zaposleni->ime }} {{ $termin->zaposleni->prezime }}</td>
                    <td>{{ $termin->usluga->naziv }}</td>
                    <td><span class="badge text-bg-secondary badge-status">{{ ['zakazan' => 'Zakazan', 'realizovan' => 'Realizovan', 'otkazan' => 'Otkazan'][$termin->status] ?? $termin->status }}</span></td>
                    <td><a href="{{ route('termini.show', $termin) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">Nema termina za prikaz.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
