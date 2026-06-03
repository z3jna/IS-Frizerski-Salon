@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Uplate</h1>
    <a href="{{ route('uplate.create') }}" class="btn btn-primary">Nova uplata</a>
</div>
<div class="table-panel p-3">
    <table class="table align-middle">
        <thead><tr><th>Datum</th><th>Račun</th><th>Klijent</th><th>Iznos</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($uplate as $uplata)
            <tr>
                <td>{{ $uplata->datum_uplate->format('d.m.Y') }}</td>
                <td>#{{ $uplata->racun_id }}</td>
                <td>{{ $uplata->racun->termin->klijent->ime }} {{ $uplata->racun->termin->klijent->prezime }}</td>
                <td>{{ number_format($uplata->iznos, 2) }}</td>
                <td>{{ ['uspesno' => 'Uspešno', 'na_cekanju' => 'Na čekanju', 'odbijeno' => 'Odbijeno'][$uplata->status_transakcije] ?? $uplata->status_transakcije }}</td>
                <td class="text-end"><a href="{{ route('uplate.show', $uplata) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Nema uplata.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $uplate->links() }}
</div>
@endsection
