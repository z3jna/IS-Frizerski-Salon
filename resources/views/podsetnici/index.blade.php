@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Podsetnici</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('podsetnici.create') }}" class="btn btn-primary">Novi podsetnik</a>
    @endif
</div>
<div class="table-panel p-3">
    <table class="table align-middle">
        <thead><tr><th>Datum slanja</th><th>Klijent</th><th>Tip</th><th>Status</th><th>Sadržaj</th><th></th></tr></thead>
        <tbody>
        @forelse($podsetnici as $podsetnik)
            <tr>
                <td>{{ $podsetnik->datum_slanja->format('d.m.Y H:i') }}</td>
                <td>{{ $podsetnik->klijent->ime }} {{ $podsetnik->klijent->prezime }}</td>
                <td>{{ $podsetnik->tip_podsetnika }}</td>
                <td>{{ ['planiran' => 'Planiran', 'poslat' => 'Poslat', 'neuspesan' => 'Neuspešan'][$podsetnik->status] ?? $podsetnik->status }}</td>
                <td>{{ \Illuminate\Support\Str::limit($podsetnik->sadrzaj, 80) }}</td>
                <td class="text-end"><a href="{{ route('podsetnici.show', $podsetnik) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Nema podsetnika.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $podsetnici->links() }}
</div>
@endsection
