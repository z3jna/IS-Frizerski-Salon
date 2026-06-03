@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Podsetnik #{{ $podsetnik->id }}</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('podsetnici.edit', $podsetnik) }}" class="btn btn-outline-primary">Izmeni</a>
    @endif
</div>
<div class="page-panel p-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Klijent</dt><dd class="col-sm-9">{{ $podsetnik->klijent->ime }} {{ $podsetnik->klijent->prezime }}</dd>
        <dt class="col-sm-3">Termin</dt><dd class="col-sm-9">{{ $podsetnik->termin ? '#'.$podsetnik->termin->id.' - '.$podsetnik->termin->usluga->naziv : '-' }}</dd>
        <dt class="col-sm-3">Datum slanja</dt><dd class="col-sm-9">{{ $podsetnik->datum_slanja->format('d.m.Y H:i') }}</dd>
        <dt class="col-sm-3">Tip</dt><dd class="col-sm-9">{{ $podsetnik->tip_podsetnika }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ $podsetnik->status }}</dd>
        <dt class="col-sm-3">Sadržaj</dt><dd class="col-sm-9">{{ $podsetnik->sadrzaj }}</dd>
    </dl>
</div>
@endsection
