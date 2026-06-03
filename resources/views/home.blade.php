@extends('layouts.app')

@section('content')
<section class="home-hero">
    <img class="home-hero__image" src="{{ asset('images/salon-hero.jpg') }}" alt="Enterijer frizerskog salona">
    <div class="home-hero__content">
        <p class="home-eyebrow">Studio za kosu i negu</p>
        <h1>Frizerski salon</h1>
        <p class="home-lead">Moderan salon za sisanje, feniranje, bojenje i tretmane koji cuvaju zdrav izgled kose.</p>
        <div class="home-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Otvori dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Prijava</a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Registracija</a>
            @endauth
        </div>
    </div>
    <div class="home-hero__stats" aria-label="Istaknute usluge">
        <span>Sisanje</span>
        <span>Kolor</span>
        <span>Tretmani</span>
    </div>
</section>

<section class="home-section">
    <div class="home-section__header">
        <p class="home-eyebrow">Usluge</p>
        <h2>Za svaki dan, vazan dogadjaj i potpunu promenu stila.</h2>
    </div>

    <div class="service-grid">
        <article class="service-card">
            <img src="{{ asset('images/salon-cut.jpg') }}" alt="Frizer sisa kosu u salonu">
            <div>
                <h3>Precizno sisanje</h3>
                <p>Linije i oblik koji prate lice, navike i ritam odrzavanja.</p>
            </div>
        </article>
        <article class="service-card">
            <img src="{{ asset('images/salon-wash.jpg') }}" alt="Pranje i nega kose u salonu">
            <div>
                <h3>Nega i tretmani</h3>
                <p>Dubinska hidratacija, obnova i sjaj bez opterecenja kose.</p>
            </div>
        </article>
        <article class="service-card service-card--feature">
            <div>
                <span class="service-card__badge">Online evidencija</span>
                <h3>Termini, klijenti i tretmani na jednom mestu</h3>
                <p>Salon tim moze da vodi zakazivanja, racune, uplate i istoriju tretmana kroz aplikaciju.</p>
            </div>
            @auth
                <a href="{{ route('termini.index') }}" class="btn btn-outline-primary">Pregled termina</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary">Udji u aplikaciju</a>
            @endauth
        </article>
    </div>
</section>
@endsection
