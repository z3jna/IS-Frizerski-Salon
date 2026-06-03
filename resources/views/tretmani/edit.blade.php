@extends('layouts.app')

@section('content')
<div class="page-panel p-4">
    <h1 class="h3 mb-4">Izmena evidencije tretmana</h1>
    <form method="POST" action="{{ route('tretmani.update', $tretman) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('tretmani._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Sačuvaj</button>
            <a href="{{ route('tretmani.show', $tretman) }}" class="btn btn-outline-secondary">Nazad</a>
        </div>
    </form>
</div>
@endsection
