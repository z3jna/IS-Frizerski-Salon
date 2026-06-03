@extends('layouts.app')

@section('content')
<div class="page-panel p-4">
    <h1 class="h3 mb-4">Nova uplata</h1>
    <form method="POST" action="{{ route('uplate.store') }}">
        @csrf
        @include('uplate._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Sačuvaj</button>
            <a href="{{ route('uplate.index') }}" class="btn btn-outline-secondary">Nazad</a>
        </div>
    </form>
</div>
@endsection
