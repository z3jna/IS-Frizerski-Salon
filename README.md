# Informacioni sistem za frizerski salon

Laravel aplikacija za upravljanje klijentima, zaposlenima, uslugama, terminima, tretmanima, fotografijama, racunima, uplatama, podsetnicima i izvestajima.

## Lokalno pokretanje

```bash
composer install
npm install
npm run build
php artisan migrate --seed
php artisan serve
```

Ako koristite upload fotografija, pokrenite i:

```bash
php artisan storage:link
```

## Pocetni nalozi

- Administrator: `admin@salon.test` / `password`
- Zaposleni: `mila@salon.test` / `password`
- Zaposleni: `marko@salon.test` / `password`
- Zaposleni: `ivana@salon.test` / `password`
- Zaposleni: `stefan@salon.test` / `password`
- Klijent: `ana@salon.test` / `password`
- Klijent: `jelena@salon.test` / `password`
- Klijent: `marija@salon.test` / `password`
- Klijent: `sofija@salon.test` / `password`
- Klijent: `nikola@salon.test` / `password`
- Klijent: `tamara@salon.test` / `password`
- Klijent: `katarina@salon.test` / `password`
- Klijent: `lazar@salon.test` / `password`
- Klijent: `milica@salon.test` / `password`
- Klijent: `sanja@salon.test` / `password`

Seed pravi demo bazu sa 15 korisnika, 4 zaposlena, 10 klijenata, 16 usluga, 20 termina, 7 realizovanih tretmana, fotografijama tretmana, racunima, uplatama i podsetnicima.

Za potpuno cistu bazu pokrenite:

```bash
php artisan migrate:fresh --seed --force
```

Za produkciju/Railway koristite ovu komandu samo ako zelite da obrisete sve postojece podatke i napravite novu demo bazu od nule.

## Railway deploy

Repo je spreman za Railway preko `railway.toml`.

1. U Railway napraviti novi projekat i povezati GitHub repo.
2. Dodati PostgreSQL ili MySQL servis.
3. U app servisu dodati varijable:

```env
APP_NAME="Frizerski Salon"
APP_ENV=production
APP_KEY=base64:GENERISANI_APP_KEY
APP_DEBUG=false
APP_URL=https://tvoj-domen.up.railway.app
LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=Monolog\\Formatter\\JsonFormatter
DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

`APP_KEY` generisite lokalno sa:

```bash
php artisan key:generate --show
```

Railway build pokrece `npm run build`, a pre deploy skripta pokrece `php artisan migrate --force --seed` i Laravel cache komande. Seeder je idempotentan, pa redeploy ne pravi duplikate.

Za javni URL u Railway dashboard-u otvorite service settings i generisite domain u Networking sekciji.
