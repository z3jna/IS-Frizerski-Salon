@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4">Izveštaji</h1>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="metric-card"><div class="label">Zakazani termini</div><div class="value">{{ $stats['zakazani'] }}</div></div></div>
    <div class="col-md-3"><div class="metric-card"><div class="label">Realizovani termini</div><div class="value">{{ $stats['realizovani'] }}</div></div></div>
    <div class="col-md-3"><div class="metric-card"><div class="label">Otkazani termini</div><div class="value">{{ $stats['otkazani'] }}</div></div></div>
    <div class="col-md-3"><div class="metric-card"><div class="label">Ukupni prihodi</div><div class="value">{{ number_format($stats['prihodi'], 2) }}</div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="table-panel p-3">
            <h2 class="h5">Najtraženije usluge</h2>
            <table class="table">
                <thead><tr><th>Usluga</th><th>Tip</th><th>Broj termina</th></tr></thead>
                <tbody>
                @foreach($najtrazenijeUsluge as $usluga)
                    <tr><td>{{ $usluga->naziv }}</td><td>{{ $usluga->tip_usluge }}</td><td>{{ $usluga->termini_count }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-panel p-3">
            <h2 class="h5">Učinak zaposlenih</h2>
            <table class="table">
                <thead><tr><th>Zaposleni</th><th>Zakazani</th><th>Realizovani</th><th>Otkazani</th></tr></thead>
                <tbody>
                @foreach($ucinakZaposlenih as $radnik)
                    <tr>
                        <td>{{ $radnik->ime }} {{ $radnik->prezime }}</td>
                        <td>{{ $radnik->zakazani_count }}</td>
                        <td>{{ $radnik->realizovani_count }}</td>
                        <td>{{ $radnik->otkazani_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
