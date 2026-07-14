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

Angular delovi su integrisani u isti projekat. Lokalno se otvaraju preko Vite dev servera: `http://127.0.0.1:4200/login`, `http://127.0.0.1:4200/register` i `http://127.0.0.1:4200/termini/create`. Laravel rute na `8000` preusmeravaju na te Angular stranice. Na Railway produkciji isti ekrani se serviraju preko `APP_URL` domena kroz `npm run build`.

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

CRUD: `/api/klijenti`, `/api/usluge`, `/api/zaposleni`, `/api/termini`, `/api/tretmani`, `/api/racuni`.

Raspored: `GET /api/dostupni-termini`, `GET /api/termini/klijent/{id}`, `GET /api/termini/zaposleni/{id}`.

## Pravila zakazivanja

Datum termina mora biti danas ili u buducnosti. Usluga, klijent i zaposleni moraju postojati. Kraj termina se racuna na osnovu trajanja usluge. Zaposleni ne moze imati dva aktivna termina koji se preklapaju. Dozvoljeni statusi su `zakazan`, `realizovan`, `otkazan`.

## Smoke test

Provereno lokalno preko HTTP zahteva: login, `GET /api/user`, `GET /api/usluge`, `GET /api/dostupni-termini`, `POST /api/termini`, `PUT /api/termini/{id}`, `GET /api/termini/klijent/{id}`, `GET /api/termini/zaposleni/{id}`, `DELETE /api/termini/{id}`.

Rezultat: svi smoke test koraci su prosli.

## Defekti i napomene

`npm audit` prijavljuje 8 ranjivosti visokog nivoa u JavaScript zavisnostima. Nije pokretan `npm audit fix --force` zato sto moze da uvede breaking promene u build alatima.
