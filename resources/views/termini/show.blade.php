@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Termin #{{ $termin->id }}</h1>
    <div class="d-flex gap-2">
        @if(auth()->user()->isAdmin() || auth()->user()->isZaposleni())
            <a href="{{ route('termini.edit', $termin) }}" class="btn btn-outline-primary">Izmeni</a>
            @if(!$termin->evidencijaTretmana)
                <a href="{{ route('tretmani.create', ['termin_id' => $termin->id]) }}" class="btn btn-primary">Evidentiraj tretman</a>
            @endif
        @endif
        @if($termin->status !== 'otkazan' && $termin->status !== 'realizovan')
            <form method="POST" action="{{ route('termini.cancel', $termin) }}">
                @csrf @method('PATCH')
                <button class="btn btn-outline-danger">Otkaži</button>
            </form>
        @endif
    </div>
</div>
<div class="page-panel p-4 mb-3">
    <dl class="row mb-0">
        <dt class="col-sm-3">Datum</dt><dd class="col-sm-9">{{ $termin->datum->format('d.m.Y') }}</dd>
        <dt class="col-sm-3">Vreme</dt><dd class="col-sm-9">{{ substr($termin->vreme_pocetka, 0, 5) }} - {{ substr($termin->vreme_zavrsetka, 0, 5) }}</dd>
        <dt class="col-sm-3">Klijent</dt><dd class="col-sm-9">{{ $termin->klijent->ime }} {{ $termin->klijent->prezime }}</dd>
        <dt class="col-sm-3">Zaposleni</dt><dd class="col-sm-9">{{ $termin->zaposleni->ime }} {{ $termin->zaposleni->prezime }}</dd>
        <dt class="col-sm-3">Usluga</dt><dd class="col-sm-9">{{ $termin->usluga->naziv }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ['zakazan' => 'Zakazan', 'realizovan' => 'Realizovan', 'otkazan' => 'Otkazan'][$termin->status] ?? $termin->status }}</dd>
        <dt class="col-sm-3">Napomena</dt><dd class="col-sm-9">{{ $termin->napomena ?? '-' }}</dd>
    </dl>
</div>
@if($termin->evidencijaTretmana)
    <a href="{{ route('tretmani.show', $termin->evidencijaTretmana) }}" class="btn btn-outline-primary">Otvori evidenciju tretmana</a>
@endif
@if($termin->racun)
    <a href="{{ route('racuni.show', $termin->racun) }}" class="btn btn-outline-primary">Otvori račun</a>
@elseif(auth()->user()->isAdmin() && $termin->status === 'realizovan')
    <a href="{{ route('racuni.create', ['termin_id' => $termin->id]) }}" class="btn btn-primary">Generiši račun</a>
@endif
@endsection
