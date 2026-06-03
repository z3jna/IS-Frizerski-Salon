@extends('layouts.app')

@section('content')
<div class="page-panel p-4">
    <h1 class="h3 mb-4">Zakazivanje termina</h1>
    <form method="POST" action="{{ route('termini.store') }}">
        @csrf
        @include('termini._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Zakaži</button>
            <a href="{{ route('termini.index') }}" class="btn btn-outline-secondary">Nazad</a>
        </div>
    </form>
</div>
@endsection
