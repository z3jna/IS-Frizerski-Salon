@extends('layouts.app')

@section('content')
<section class="home-hero">
    <img class="home-hero__image" src="{{ asset('images/salon-hero.jpg') }}" alt="Enterijer frizerskog salona">
    <div class="home-hero__content">
        <p class="home-eyebrow">Studio za kosu i negu</p>
        <h1>Frizerski salon</h1>
        <p class="home-lead">Moderan salon za šišanje, feniranje, bojenje i tretmane koji čuvaju zdrav izgled kose.</p>
        <div class="home-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Otvori dashboard</a>
            @else
                <a href="/login" class="btn btn-primary btn-lg">Prijava</a>
                <a href="/register" class="btn btn-outline-light btn-lg">Registracija</a>
            @endauth
        </div>
    </div>
    <div class="home-hero__stats" aria-label="Istaknute usluge">
        <span>Šišanje</span>
        <span>Kolor</span>
        <span>Tretmani</span>
    </div>
</section>

<section class="home-section">
    <div class="home-section__header">
        <p class="home-eyebrow">Usluge</p>
        <h2>Za svaki dan, važan događaj i potpunu promenu stila.</h2>
    </div>

    <div class="service-grid">
        <article class="service-card">
            <img src="{{ asset('images/salon-cut.jpg') }}" alt="Frizer šiša kosu u salonu">
            <div>
                <h3>Precizno šišanje</h3>
                <p>Linije i oblik koji prate lice, navike i ritam održavanja.</p>
            </div>
        </article>
        <article class="service-card">
            <img src="{{ asset('images/salon-wash.jpg') }}" alt="Pranje i nega kose u salonu">
            <div>
                <h3>Nega i tretmani</h3>
                <p>Dubinska hidratacija, obnova i sjaj bez opterećenja kose.</p>
            </div>
        </article>
        <article class="service-card service-card--feature">
            <div>
                <span class="service-card__badge">Online evidencija</span>
                <h3>Termini, klijenti i tretmani na jednom mestu</h3>
                <p>Salon tim može da vodi zakazivanja, račune, uplate i istoriju tretmana kroz aplikaciju.</p>
            </div>
            @auth
                <a href="{{ route('termini.index') }}" class="btn btn-outline-primary">Pregled termina</a>
            @else
                <a href="/login" class="btn btn-outline-primary">Uđi u aplikaciju</a>
            @endauth
        </article>
    </div>
</section>
@endsection
