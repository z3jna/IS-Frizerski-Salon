@extends('layouts.app')

@section('content')
<div class="page-panel p-4">
    <h1 class="h3 mb-4">Novi zaposleni</h1>
    <form method="POST" action="{{ route('zaposleni.store') }}">
        @csrf
        @include('zaposleni._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Sačuvaj</button>
            <a href="{{ route('zaposleni.index') }}" class="btn btn-outline-secondary">Nazad</a>
        </div>
    </form>
</div>
@endsection
