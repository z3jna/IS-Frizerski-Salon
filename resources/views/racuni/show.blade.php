@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Račun #{{ $racun->id }}</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('racuni.edit', $racun) }}" class="btn btn-outline-primary">Izmeni</a>
    @endif
</div>
<div class="page-panel p-4 mb-3">
    <dl class="row mb-0">
        <dt class="col-sm-3">Klijent</dt><dd class="col-sm-9">{{ $racun->termin->klijent->ime }} {{ $racun->termin->klijent->prezime }}</dd>
        <dt class="col-sm-3">Usluga</dt><dd class="col-sm-9">{{ $racun->termin->usluga->naziv }}</dd>
        <dt class="col-sm-3">Datum</dt><dd class="col-sm-9">{{ $racun->datum_izdavanja->format('d.m.Y') }}</dd>
        <dt class="col-sm-3">Iznos</dt><dd class="col-sm-9">{{ number_format($racun->ukupan_iznos, 2) }}</dd>
        <dt class="col-sm-3">Način plaćanja</dt><dd class="col-sm-9">{{ $racun->nacin_placanja ?? '-' }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ['neplaceno' => 'Neplaćeno', 'delimicno' => 'Delimično', 'placeno' => 'Plaćeno'][$racun->status_placanja] ?? $racun->status_placanja }}</dd>
    </dl>
</div>
<div class="table-panel p-3">
    <h2 class="h5">Uplate</h2>
    <table class="table">
        <thead><tr><th>Datum</th><th>Iznos</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($racun->uplate as $uplata)
            <tr><td>{{ $uplata->datum_uplate->format('d.m.Y') }}</td><td>{{ number_format($uplata->iznos, 2) }}</td><td>{{ ['uspesno' => 'Uspešno', 'na_cekanju' => 'Na čekanju', 'odbijeno' => 'Odbijeno'][$uplata->status_transakcije] ?? $uplata->status_transakcije }}</td></tr>
        @empty
            <tr><td colspan="3" class="text-muted">Nema uplata.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
