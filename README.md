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
- Klijent: `ana@salon.test` / `password`

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
