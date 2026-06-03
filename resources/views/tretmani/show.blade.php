@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tretman #{{ $tretman->id }}</h1>
    @if(auth()->user()->isAdmin() || auth()->user()->isZaposleni())
        <a href="{{ route('tretmani.edit', $tretman) }}" class="btn btn-outline-primary">Izmeni</a>
    @endif
</div>
<div class="page-panel p-4 mb-3">
    <dl class="row mb-0">
        <dt class="col-sm-3">Klijent</dt><dd class="col-sm-9">{{ $tretman->termin->klijent->ime }} {{ $tretman->termin->klijent->prezime }}</dd>
        <dt class="col-sm-3">Usluga</dt><dd class="col-sm-9">{{ $tretman->termin->usluga->naziv }}</dd>
        <dt class="col-sm-3">Datum</dt><dd class="col-sm-9">{{ $tretman->datum->format('d.m.Y') }}</dd>
        <dt class="col-sm-3">Opis</dt><dd class="col-sm-9">{{ $tretman->opis_tretmana }}</dd>
        <dt class="col-sm-3">Nijansa</dt><dd class="col-sm-9">{{ $tretman->nijansa ?? '-' }}</dd>
        <dt class="col-sm-3">Proizvođač</dt><dd class="col-sm-9">{{ $tretman->proizvodjac ?? '-' }}</dd>
        <dt class="col-sm-3">Formula</dt><dd class="col-sm-9">{{ $tretman->formula ?? '-' }}</dd>
        <dt class="col-sm-3">Preparati</dt><dd class="col-sm-9">{{ $tretman->korisceni_preparati ?? '-' }}</dd>
        <dt class="col-sm-3">Napomena</dt><dd class="col-sm-9">{{ $tretman->napomena ?? '-' }}</dd>
    </dl>
</div>
<div class="page-panel p-4">
    <h2 class="h5 mb-3">Fotografije pre/posle</h2>
    @include('tretmani._photo_gallery', ['photos' => $tretman->fotografije])
</div>
@endsection
