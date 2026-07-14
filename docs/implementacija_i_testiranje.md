# Implementacija i testiranje

## Pokretanje

Backend:

```bash
composer install
php artisan migrate --seed
php artisan serve
```

Angular frontend u Laravel aplikaciji:

```bash
npm install
npm run dev
```

Angular delovi su integrisani u isti Laravel projekat. Lokalno se otvaraju na `/login`, `/register` i `/termini/create` preko Laravel hosta, dok Vite servira razvojne assete. Na Railway produkciji iste relativne rute automatski koriste aktivni `APP_URL` domen.

## API autentifikacija

`POST /api/login` vraca bearer token. Za zasticene rute slati header:

```http
Authorization: Bearer <token>
Accept: application/json
```

Primer zahteva:

```json
{
  "email": "admin@salon.test",
  "password": "password"
}
```

Primer odgovora:

```json
{
  "message": "Prijava je uspesna.",
  "token": "GENERISANI_TOKEN",
  "user": {
    "id": 1,
    "email": "admin@salon.test",
    "role": "administrator"
  }
}
```

## API rute

Autentifikacija: `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/user`.

Zakazivanje: `GET /api/usluge`, `GET /api/zaposleni`, `GET /api/dostupni-termini`, `POST /api/termini`.

To je ukupno osam API ruta koje Angular stvarno koristi. Ostali Laravel CRUD procesi koriste `routes/web.php` i Blade kontrolere.

## Pravila zakazivanja

Datum termina mora biti danas ili u buducnosti. Usluga, klijent i zaposleni moraju postojati. Kraj termina se racuna na osnovu trajanja usluge. Zaposleni ne moze imati dva aktivna termina koji se preklapaju. Dozvoljeni statusi su `zakazan`, `realizovan`, `otkazan`.

## Smoke test

Provereno lokalno preko HTTP zahteva i browsera: login, `GET /api/user`, `GET /api/usluge`, `GET /api/zaposleni`, `GET /api/dostupni-termini`, `POST /api/termini` i logout. Potvrđen je i prelazak iz Angular menija na Laravel Dashboard bez ponovne prijave.

Rezultat: svi smoke test koraci su prosli.

## Defekti i napomene

`npm audit` prijavljuje 8 ranjivosti visokog nivoa u JavaScript zavisnostima. Nije pokretan `npm audit fix --force` zato sto moze da uvede breaking promene u build alatima.
