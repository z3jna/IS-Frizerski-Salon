@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Evidencija tretmana</h1>
    @if(auth()->user()->isAdmin() || auth()->user()->isZaposleni())
        <a href="{{ route('tretmani.create') }}" class="btn btn-primary">Nova evidencija</a>
    @endif
</div>
<div class="table-panel p-3">
    <table class="table align-middle">
        <thead><tr><th>Datum</th><th>Klijent</th><th>Usluga</th><th>Zaposleni</th><th>Nijansa</th><th></th></tr></thead>
        <tbody>
        @forelse($tretmani as $tretman)
            <tr>
                <td>{{ $tretman->datum->format('d.m.Y') }}</td>
                <td>{{ $tretman->termin->klijent->ime }} {{ $tretman->termin->klijent->prezime }}</td>
                <td>{{ $tretman->termin->usluga->naziv }}</td>
                <td>{{ $tretman->termin->zaposleni->ime }} {{ $tretman->termin->zaposleni->prezime }}</td>
                <td>{{ $tretman->nijansa ?? '-' }}</td>
                <td class="text-end"><a href="{{ route('tretmani.show', $tretman) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Nema evidentiranih tretmana.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $tretmani->links() }}
</div>
@endsection
