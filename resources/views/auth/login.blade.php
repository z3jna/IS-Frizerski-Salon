@extends('layouts.app')

@section('content')
<div class="auth-wrap">
    <div class="auth-visual" aria-hidden="true">
        <img src="{{ asset('images/salon-hero.jpg') }}" alt="">
    </div>
    <div class="auth-card">
        <div class="page-panel p-4">
            <p class="home-eyebrow mb-2">Dobro došli nazad</p>
            <h1 class="h4 mb-4">Prijava</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lozinka</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                    <label for="remember" class="form-check-label">Zapamti me</label>
                </div>
                <button class="btn btn-primary w-100">Prijavi se</button>
                <div class="mt-3 text-center">
                    <a href="{{ route('register') }}">Registracija klijenta</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
