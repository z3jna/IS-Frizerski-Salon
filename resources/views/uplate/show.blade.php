@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Uplata #{{ $uplata->id }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('uplate.edit', $uplata) }}" class="btn btn-outline-primary">Izmeni</a>
        <form method="POST" action="{{ route('uplate.destroy', $uplata) }}" onsubmit="return confirm('Obrisati uplatu?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">Obriši</button>
        </form>
    </div>
</div>
<div class="page-panel p-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Račun</dt><dd class="col-sm-9"><a href="{{ route('racuni.show', $uplata->racun) }}">#{{ $uplata->racun_id }}</a></dd>
        <dt class="col-sm-3">Klijent</dt><dd class="col-sm-9">{{ $uplata->racun->termin->klijent->ime }} {{ $uplata->racun->termin->klijent->prezime }}</dd>
        <dt class="col-sm-3">Datum</dt><dd class="col-sm-9">{{ $uplata->datum_uplate->format('d.m.Y') }}</dd>
        <dt class="col-sm-3">Iznos</dt><dd class="col-sm-9">{{ number_format($uplata->iznos, 2) }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ['uspesno' => 'Uspešno', 'na_cekanju' => 'Na čekanju', 'odbijeno' => 'Odbijeno'][$uplata->status_transakcije] ?? $uplata->status_transakcije }}</dd>
    </dl>
</div>
@endsection
