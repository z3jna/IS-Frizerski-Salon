@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Klijenti</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('klijenti.create') }}" class="btn btn-primary">Novi klijent</a>
    @endif
</div>
<div class="table-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Ime</th><th>Email</th><th>Telefon</th><th>Preferencije</th><th></th></tr></thead>
            <tbody>
            @forelse($klijenti as $klijent)
                <tr>
                    <td>{{ $klijent->ime }} {{ $klijent->prezime }}</td>
                    <td>{{ $klijent->user?->email ?? '-' }}</td>
                    <td>{{ $klijent->telefon ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($klijent->preferencije, 60) }}</td>
                    <td class="text-end"><a href="{{ route('klijenti.show', $klijent) }}" class="btn btn-sm btn-outline-primary">Detalji</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">Nema klijenata.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $klijenti->links() }}
</div>
@endsection
