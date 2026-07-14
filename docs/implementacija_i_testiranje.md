# Implementacija i testiranje

## Izabrani procesi

### Proces 1: registracija i prijava klijenta

Angular forme prikupljaju korisnički unos i šalju JSON Laravel API-ju. Backend validira obavezna polja, format i jedinstvenost emaila, potvrdu lozinke i minimalnu dužinu od 8 karaktera. Uspešna registracija u jednoj transakciji kreira `users` i `klijenti` zapis. Uspešna prijava proverava hash lozinke i izdaje novi API token.

### Proces 2: zakazivanje termina

Proces povezuje klijenta, zaposlenog, uslugu, datum i vreme. Angular najpre učitava dostupne usluge i zaposlene, zatim za izabranu kombinaciju traži slobodne slotove. Laravel računa završetak prema trajanju usluge i odbija prošlo vreme, termin van radnog vremena ili preklapanje.

## Arhitektura

- Laravel REST API backend u `routes/api.php`;
- Angular frontend sa tri standalone komponente i Angular Routerom;
- JSON komunikacija preko `HttpClient`;
- Bearer autentifikacija preko `api.token` middleware-a;
- `AuthService` za autentifikaciju i `BookingService` za ceo proces zakazivanja.

`routes/web.php` ima samo četiri GET rute koje u produkciji serviraju Angular shell. Ne postoje Blade dashboard, CRUD stranice, Laravel auth sesija niti `/angular-*` bridge rute.

## API ugovor

| Metod | Ruta | Autentifikacija | Namena |
|---|---|---|---|
| POST | `/api/register` | javna | registracija klijenta |
| POST | `/api/login` | javna | prijava i izdavanje tokena |
| POST | `/api/logout` | Bearer | poništavanje tokena |
| GET | `/api/user` | Bearer | provera tokena i trenutni korisnik |
| GET | `/api/zakazivanje/opcije` | Bearer | dostupne usluge i zaposleni |
| GET | `/api/zakazivanje/dostupni-termini` | Bearer | slobodni slotovi |
| POST | `/api/zakazivanje/termini` | Bearer | kreiranje termina |

Primer zahteva za zakazivanje:

```json
{
  "datum": "2026-08-15",
  "vreme_pocetka": "10:00",
  "zaposleni_id": 1,
  "usluga_id": 1,
  "napomena": "Prvi dolazak"
}
```

`klijent_id` nije deo ulaza. API ga uzima iz korisnika pronađenog pomoću Bearer tokena.

## Klase ekvivalencije

### Registracija i prijava

- validan jedinstven email / nevalidan format / već zauzet email;
- lozinka sa najmanje 8 karaktera / lozinka kraća od 8;
- podudarna / nepodudarna potvrda lozinke;
- tačni / netačni kredencijali;
- validan / nedostajući / neispravan token.

### Zakazivanje

- budući / prošli datum;
- potpuno / nepotpuno popunjen zahtev;
- dostupna / nedostupna usluga;
- slobodan / preklopljen termin;
- vreme unutar / pre / posle radnog vremena;
- klijent / druga uloga korisnika.

## Granične vrednosti

- dužina lozinke: 7 je nevalidno, 8 je validno;
- početak radnog vremena: 08:00 je validan;
- kraj radnog vremena: završetak tačno u 20:00 je validan, završetak posle 20:00 nije;
- današnji termin mora početi strogo posle trenutnog vremena;
- početak drugog termina tačno na završetku prvog ne predstavlja preklapanje.

## Automatizovani testovi

Komanda:

```bash
php artisan test
```

Poslednje izvršenje nakon refaktorisanja: **15 testova je prošlo, 55 assertiona, 0 padova**. Testovi koriste SQLite bazu u memoriji i `RefreshDatabase`, tako da ne menjaju razvojne podatke.

Obuhvaćeni su registracija na granici od 8 karaktera, lozinka od 7 karaktera, duplikat emaila, validna i nevalidna prijava, pristup bez tokena, odjava, opcije zakazivanja, prošli datum, nepotpun unos, granice radnog vremena, uspešno zakazivanje i preklapanje.

## Postman i manuelno testiranje

Kolekcija `docs/postman_frizerski_salon_api.json` sadrži 15 numerisanih test primera. Prvo se izvršava validna prijava koja čuva token, zatim opcije zakazivanja čuvaju ID usluge i zaposlenog, a slobodni slotovi određuju vreme za validno zakazivanje i test preklapanja.

Manuelni scenario:

1. otvoriti `/register` i proveriti validne i nevalidne unose;
2. otvoriti `/login`, proveriti pogrešnu i ispravnu lozinku;
3. potvrditi preusmerenje na `/termini/create`;
4. proveriti da je izbor vremena zaključan dok nisu izabrani zaposleni, usluga i datum;
5. izabrati ponuđeni slot i zakazati termin;
6. potvrditi poruku o uspehu i novo učitavanje slobodnih slotova;
7. obrisati token iz `localStorage` i potvrditi povratak na login.

Poslednji browser smoke test je izvršen 14.07.2026. i prošao je sve tri stranice. Potvrđeni su: greška za nevalidnu prijavu, validna prijava, zabrana pristupa zakazivanju bez tokena, odbijanje lozinke od 7 karaktera, registracija sa 8 karaktera, učitavanje opcija i slobodnih slotova i uspešno zakazivanje. Testni korisnik i termin su nakon provere uklonjeni iz razvojne baze.

## Evidentirani defekti

- Uklonjen je paralelni session/token tok koji je duplirao autentifikaciju.
- Uklonjeni su nekorišćeni Angular servisi i API CRUD rute van izabranih procesa.
- `klijent_id` se više ne prihvata iz forme, čime je sprečeno zakazivanje u ime drugog klijenta.
- Opcije zakazivanja vraćaju samo usluge čija je `dostupnost` uključena.

`npm run build` prolazi. NPM audit trenutno prijavljuje 7 ranjivosti visokog nivoa u build zavisnostima; nije korišćen `npm audit fix --force` jer bi mogao uvesti breaking promene.
