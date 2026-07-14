# Informacioni sistem za frizerski salon

Projekat za predmet Programiranje internet aplikacija realizuje dva procesa iz SSA modela:

1. registraciju i prijavu klijenta sa validacijom unosa;
2. zakazivanje termina sa proverom raspoloživosti i poslovnih pravila.

Arhitektura je odvojena: Laravel 11 vraća isključivo JSON odgovore, a Angular 18 predstavlja jedini korisnički interfejs. Modeli, migracije i seed podaci šireg EER modela ostaju u projektu, ali nisu izloženi kao dodatni UI procesi.

## Pokretanje

```bash
composer install
npm install
php artisan migrate --seed
php artisan serve
```

U drugom terminalu pokrenuti Angular/Vite frontend:

```bash
npm run dev
```

Frontend je dostupan na `http://127.0.0.1:4200`, a Laravel API na `http://127.0.0.1:8000`.

Angular rute:

- `/login`
- `/register`
- `/termini/create`

## API

Javne rute:

- `POST /api/register`
- `POST /api/login`

Rute zaštićene Bearer tokenom:

- `POST /api/logout`
- `GET /api/user`
- `GET /api/zakazivanje/opcije`
- `GET /api/zakazivanje/dostupni-termini`
- `POST /api/zakazivanje/termini`

Angular čuva token u `localStorage` pod ključem `salon_api_token`. Interceptor ga šalje kroz `Authorization: Bearer <token>`. Klijent ne šalje `klijent_id`; backend ga određuje iz tokena.

## Poslovna pravila zakazivanja

- termin mora biti u budućnosti;
- radno vreme je od 08:00 do 20:00;
- završetak se računa iz trajanja izabrane usluge;
- usluga mora biti dostupna;
- zaposleni ne može imati preklopljene aktivne termine;
- termin može zakazati samo prijavljeni klijent.

## Testiranje

```bash
php artisan test
npm run build
php artisan route:list --except-vendor
```

Feature paket trenutno sadrži 15 prolaznih testova sa 55 assertiona. Postman kolekcija sa 15 test primera nalazi se u `docs/postman_frizerski_salon_api.json`.

Demo klijentski nalog:

```text
ana@salon.test / password
```

Detaljan opis procesa, klasa ekvivalencije i graničnih vrednosti nalazi se u `docs/implementacija_i_testiranje.md` i Word dokumentaciji.

## Railway

Build koristi `npm run build`, a Laravel u produkciji servira isti Angular shell za `/`, `/login`, `/register` i `/termini/create`. Poseban `ANGULAR_URL` nije potreban.
