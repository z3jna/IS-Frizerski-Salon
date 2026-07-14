@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Katalog usluga</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('usluge.create') }}" class="btn btn-primary">Nova usluga</a>
    @endif
</div>
<div class="table-panel p-3">
    <table class="table align-middle">
        <thead><tr><th>Naziv</th><th>Tip</th><th>Trajanje</th><th>Cena</th><th>Dostupnost</th><th></th></tr></thead>
        <tbody>
        @forelse($usluge as $usluga)
            <tr>
                <td>{{ $usluga->naziv }}</td>
                <td>{{ $usluga->tip_usluge }}</td>
                <td>{{ $usluga->trajanje_minuta }} min</td>
                <td>{{ number_format($usluga->cena, 2) }}</td>
                <td>{{ $usluga->dostupnost ? 'Dostupna' : 'Nedostupna' }}</td>
                <td class="text-end"><a href="{{ route('usluge.show', $usluga) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Nema usluga.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $usluge->links() }}
</div>
@endsection
