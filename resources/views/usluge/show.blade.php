@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $usluga->naziv }}</h1>
    @if(auth()->user()->isAdmin())
        <div class="d-flex gap-2">
            <a href="{{ route('usluge.edit', $usluga) }}" class="btn btn-outline-primary">Izmeni</a>
            <form method="POST" action="{{ route('usluge.destroy', $usluga) }}" onsubmit="return confirm('Obrisati uslugu?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Obriši</button>
            </form>
        </div>
    @endif
</div>
<div class="page-panel p-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Tip</dt><dd class="col-sm-9">{{ $usluga->tip_usluge }}</dd>
        <dt class="col-sm-3">Trajanje</dt><dd class="col-sm-9">{{ $usluga->trajanje_minuta }} min</dd>
        <dt class="col-sm-3">Cena</dt><dd class="col-sm-9">{{ number_format($usluga->cena, 2) }}</dd>
        <dt class="col-sm-3">Dostupnost</dt><dd class="col-sm-9">{{ $usluga->dostupnost ? 'Dostupna' : 'Nedostupna' }}</dd>
        <dt class="col-sm-3">Opis</dt><dd class="col-sm-9">{{ $usluga->opis ?? '-' }}</dd>
    </dl>
</div>
@endsection
