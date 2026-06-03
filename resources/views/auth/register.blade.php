@extends('layouts.app')

@section('content')
<div class="auth-wrap auth-wrap--wide">
    <div class="auth-visual auth-visual--register" aria-hidden="true">
        <img src="{{ asset('images/salon-wash.jpg') }}" alt="">
    </div>
    <div class="auth-card">
        <div class="page-panel p-4">
            <p class="home-eyebrow mb-2">Novi klijent</p>
            <h1 class="h4 mb-4">Registracija klijenta</h1>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ime</label>
                        <input name="ime" value="{{ old('ime') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prezime</label>
                        <input name="prezime" value="{{ old('prezime') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefon</label>
                        <input name="telefon" value="{{ old('telefon') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adresa</label>
                        <input name="adresa" value="{{ old('adresa') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Datum rodjenja</label>
                        <input type="date" name="datum_rodjenja" value="{{ old('datum_rodjenja') }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Preferencije</label>
                        <textarea name="preferencije" class="form-control" rows="2">{{ old('preferencije') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lozinka</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Potvrda lozinke</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="{{ route('login') }}">Vec imate nalog?</a>
                    <button class="btn btn-primary">Registruj se</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
