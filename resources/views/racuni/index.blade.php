@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Računi</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('racuni.create') }}" class="btn btn-primary">Novi račun</a>
    @endif
</div>
<div class="table-panel p-3">
    <table class="table align-middle">
        <thead><tr><th>Datum</th><th>Klijent</th><th>Usluga</th><th>Iznos</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($racuni as $racun)
            <tr>
                <td>{{ $racun->datum_izdavanja->format('d.m.Y') }}</td>
                <td>{{ $racun->termin->klijent->ime }} {{ $racun->termin->klijent->prezime }}</td>
                <td>{{ $racun->termin->usluga->naziv }}</td>
                <td>{{ number_format($racun->ukupan_iznos, 2) }}</td>
                <td>{{ $racun->status_placanja }}</td>
                <td class="text-end"><a href="{{ route('racuni.show', $racun) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Nema računa.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $racuni->links() }}
</div>
@endsection
