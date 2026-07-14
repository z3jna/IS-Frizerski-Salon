<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Frizerski Salon') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-xl navbar-dark app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">Frizerski salon</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Početna</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('usluge.index') }}">Usluge</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('termini.index') }}">Termini</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tretmani.index') }}">Tretmani</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('racuni.index') }}">Računi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('podsetnici.index') }}">Podsetnici</a></li>
                    @if(auth()->user()->isAdmin() || auth()->user()->isZaposleni())
                        <li class="nav-item"><a class="nav-link" href="{{ route('klijenti.index') }}">Klijenti</a></li>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('zaposleni.index') }}">Zaposleni</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('uplate.index') }}">Uplate</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('izvestaji.index') }}">Izveštaji</a></li>
                    @endif
                </ul>
                <div class="navbar-account">
                    <span class="navbar-account__label">Nalog</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm" type="submit">Odjava</button>
                    </form>
                </div>
            @else
                <ul class="navbar-nav ms-auto align-items-xl-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Početna</a></li>
                    <li class="nav-item"><a class="nav-link" href="/login">Prijava</a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-light ms-lg-2" href="/register">Registracija</a></li>
                </ul>
            @endauth
        </div>
    </div>
</nav>

<main class="app-shell">
    <div class="container-fluid px-4">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Proverite unesene podatke.</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>
</body>
</html>
