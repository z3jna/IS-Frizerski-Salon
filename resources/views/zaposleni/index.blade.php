@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Zaposleni</h1>
    <a href="{{ route('zaposleni.create') }}" class="btn btn-primary">Novi zaposleni</a>
</div>
<div class="table-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Ime</th><th>Pozicija</th><th>Telefon</th><th>Radno vreme</th><th>Realizovani termini</th><th></th></tr></thead>
            <tbody>
            @forelse($zaposleni as $radnik)
                <tr>
                    <td>{{ $radnik->ime }} {{ $radnik->prezime }}</td>
                    <td>{{ $radnik->pozicija ?? '-' }}</td>
                    <td>{{ $radnik->telefon ?? '-' }}</td>
                    <td>{{ $radnik->radno_vreme ?? '-' }}</td>
                    <td>{{ $radnik->realizovani_termini_count }}</td>
                    <td class="text-end"><a href="{{ route('zaposleni.show', $radnik) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">Nema zaposlenih.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $zaposleni->links() }}
</div>
@endsection
