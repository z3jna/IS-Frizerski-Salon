<?php

namespace Database\Seeders;

use App\Models\EvidencijaTretmana;
use App\Models\FotografijaTretmana;
use App\Models\Klijent;
use App\Models\Podsetnik;
use App\Models\Racun;
use App\Models\Termin;
use App\Models\Uplata;
use App\Models\User;
use App\Models\Usluga;
use App\Models\Zaposleni;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
        $zaposleni = $this->seedZaposleni($users);
        $klijenti = $this->seedKlijenti($users);
        $usluge = $this->seedUsluge();

        $this->seedTermini($klijenti, $zaposleni, $usluge);
    }

    private function seedUsers(): array
    {
        $password = Hash::make('password');

        $data = [
            'admin' => ['Administrator Salona', 'admin@salon.test', User::ROLE_ADMIN],
            'mila' => ['Mila Petrović', 'mila@salon.test', User::ROLE_ZAPOSLENI],
            'marko' => ['Marko Nikolić', 'marko@salon.test', User::ROLE_ZAPOSLENI],
            'ivana' => ['Ivana Ilić', 'ivana@salon.test', User::ROLE_ZAPOSLENI],
            'stefan' => ['Stefan Đorđević', 'stefan@salon.test', User::ROLE_ZAPOSLENI],
            'ana' => ['Ana Jovanović', 'ana@salon.test', User::ROLE_KLIJENT],
            'jelena' => ['Jelena Savić', 'jelena@salon.test', User::ROLE_KLIJENT],
            'marija' => ['Marija Lukić', 'marija@salon.test', User::ROLE_KLIJENT],
            'sofija' => ['Sofija Radić', 'sofija@salon.test', User::ROLE_KLIJENT],
            'nikola' => ['Nikola Pavlović', 'nikola@salon.test', User::ROLE_KLIJENT],
            'tamara' => ['Tamara Stanković', 'tamara@salon.test', User::ROLE_KLIJENT],
            'katarina' => ['Katarina Vasić', 'katarina@salon.test', User::ROLE_KLIJENT],
            'lazar' => ['Lazar Popović', 'lazar@salon.test', User::ROLE_KLIJENT],
            'milica' => ['Milica Ristić', 'milica@salon.test', User::ROLE_KLIJENT],
            'sanja' => ['Sanja Milošević', 'sanja@salon.test', User::ROLE_KLIJENT],
        ];

        $users = [];

        foreach ($data as $key => [$name, $email, $role]) {
            $users[$key] = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => $password,
                    'role' => $role,
                ],
            );
        }

        return $users;
    }

    private function seedZaposleni(array $users): array
    {
        $data = [
            'mila' => ['Mila', 'Petrović', '060/111-222', 'Senior frizer i kolorista', 'Pon-Pet 09:00-17:00', '2022-03-15', 95000],
            'marko' => ['Marko', 'Nikolić', '060/222-333', 'Barber i stilista', 'Uto-Sub 10:00-18:00', '2021-09-01', 88000],
            'ivana' => ['Ivana', 'Ilić', '060/333-444', 'Kolorista', 'Pon-Pet 12:00-20:00', '2023-01-20', 91000],
            'stefan' => ['Stefan', 'Đorđević', '060/444-555', 'Junior frizer', 'Pon-Sre 09:00-15:00, Sub 09:00-14:00', '2024-05-10', 68000],
        ];

        $zaposleni = [];

        foreach ($data as $key => [$ime, $prezime, $telefon, $pozicija, $radnoVreme, $datum, $plata]) {
            $zaposleni[$key] = Zaposleni::updateOrCreate(
                ['user_id' => $users[$key]->id],
                [
                    'ime' => $ime,
                    'prezime' => $prezime,
                    'telefon' => $telefon,
                    'pozicija' => $pozicija,
                    'radno_vreme' => $radnoVreme,
                    'datum_zaposlenja' => $datum,
                    'plata' => $plata,
                ],
            );
        }

        return $zaposleni;
    }

    private function seedKlijenti(array $users): array
    {
        $data = [
            'ana' => ['Ana', 'Jovanović', '061/333-444', 'Bulevar umetnosti 12', '1993-08-21', 'Osetljivo teme, izbegavati agresivne preparate.', 'Topli tonovi, zakazivanje posle 16h.'],
            'jelena' => ['Jelena', 'Savić', '061/111-900', 'Kralja Petra 18', '1988-02-12', 'Preferira tiše termine bez gužve.', 'Prirodno feniranje, bez jakog laka.'],
            'marija' => ['Marija', 'Lukić', '062/220-101', 'Njegoševa 7', '1996-11-04', 'Alergija na amonijak.', 'Balayage u hladnijim tonovima.'],
            'sofija' => ['Sofija', 'Radić', '063/401-778', 'Dunavska 41', '2001-05-30', null, 'Brzi termini vikendom.'],
            'nikola' => ['Nikola', 'Pavlović', '064/551-870', 'Cara Dušana 4', '1985-07-18', 'Kratko vreme za termin, dolazi tokom pauze.', 'Kratko šišanje i oblikovanje brade.'],
            'tamara' => ['Tamara', 'Stanković', '065/118-345', 'Zmaj Jovina 22', '1991-03-09', null, 'Keratin na svaka 3 meseca.'],
            'katarina' => ['Katarina', 'Vasić', '066/777-821', 'Požeška 19', '1998-09-26', 'Kosa sklona isušivanju.', 'Hidratantni tretmani i maske.'],
            'lazar' => ['Lazar', 'Popović', '064/908-332', 'Takovska 12', '1990-12-15', null, 'Barber šišanje na 3 nedelje.'],
            'milica' => ['Milica', 'Ristić', '061/909-411', 'Resavska 3', '1994-04-11', 'Ne koristiti prejak hidrogen.', 'Pramenovi i gloss tretman.'],
            'sanja' => ['Sanja', 'Milošević', '063/132-500', 'Makedonska 15', '1982-10-02', 'Dolazi sa decom, potrebna tačnost termina.', 'Kratka paž frizura.'],
        ];

        $klijenti = [];

        foreach ($data as $key => [$ime, $prezime, $telefon, $adresa, $rodjenje, $napomena, $preferencije]) {
            $klijenti[$key] = Klijent::updateOrCreate(
                ['user_id' => $users[$key]->id],
                [
                    'ime' => $ime,
                    'prezime' => $prezime,
                    'telefon' => $telefon,
                    'adresa' => $adresa,
                    'datum_rodjenja' => $rodjenje,
                    'napomena' => $napomena,
                    'preferencije' => $preferencije,
                ],
            );
        }

        return $klijenti;
    }

    private function seedUsluge(): array
    {
        $data = [
            ['Šišanje', 'Osnovna usluga', 'Žensko šišanje sa konsultacijom.', 45, 2500, true],
            ['Muško šišanje', 'Osnovna usluga', 'Precizno muško šišanje makazama i mašinicom.', 35, 1800, true],
            ['Dečije šišanje', 'Osnovna usluga', 'Brzo i pažljivo šišanje za decu.', 25, 1200, true],
            ['Feniranje', 'Stilizovanje', 'Pranje i feniranje kose.', 40, 1800, true],
            ['Svečana frizura', 'Stilizovanje', 'Frizura za svadbe, mature i događaje.', 90, 5500, true],
            ['Farbanje', 'Koloracija', 'Farbanje izrastka ili cele dužine.', 120, 6000, true],
            ['Preliv', 'Koloracija', 'Osvežavanje nijanse i sjaja.', 60, 3500, true],
            ['Balayage', 'Koloracija', 'Balayage tehnika sa toniranjem.', 180, 12000, true],
            ['Pramenovi', 'Koloracija', 'Klasični pramenovi sa folijom.', 150, 9000, true],
            ['AirTouch', 'Koloracija', 'Napredna tehnika posvetljivanja kose.', 240, 16000, true],
            ['Keratin', 'Nega kose', 'Keratin tretman za zaglađivanje kose.', 150, 10000, true],
            ['Botox kose', 'Nega kose', 'Tretman za popunjavanje i sjaj dlake.', 90, 6500, true],
            ['Nega kose', 'Nega kose', 'Dubinska hidratacija i obnova.', 60, 3500, true],
            ['Pakovanje', 'Nega kose', 'Brzo salonsko pakovanje posle pranja.', 30, 1500, true],
            ['Oblikovanje brade', 'Barber', 'Sređivanje i konturisanje brade.', 25, 1200, true],
            ['Konsultacije', 'Konsultacije', 'Konsultacije za promenu boje i plana nege.', 20, 0, true],
        ];

        $legacyNazivi = [
            'Šišanje' => 'Sisanje',
            'Muško šišanje' => 'Musko sisanje',
            'Dečije šišanje' => 'Decije sisanje',
            'Svečana frizura' => 'Svecana frizura',
        ];

        $usluge = [];

        foreach ($data as [$naziv, $tip, $opis, $trajanje, $cena, $dostupnost]) {
            $usluga = Usluga::query()
                ->whereIn('naziv', array_filter([$naziv, $legacyNazivi[$naziv] ?? null]))
                ->first() ?? new Usluga();

            $usluga->fill([
                'naziv' => $naziv,
                'tip_usluge' => $tip,
                'opis' => $opis,
                'trajanje_minuta' => $trajanje,
                'cena' => $cena,
                'dostupnost' => $dostupnost,
            ]);
            $usluga->save();

            $usluge[$naziv] = $usluga;
        }

        return $usluge;
    }

    private function seedTermini(array $klijenti, array $zaposleni, array $usluge): void
    {
        $termini = [
            [-35, '09:00', '10:30', 'realizovan', 'ana', 'mila', 'Farbanje', 'Osvežen izrastak i toniranje dužine.', '7.34', 'L Oreal Professionnel', '7.34 + 6% oksidant, 1:1.5', 'kartica', 'placeno'],
            [-28, '11:00', '12:00', 'realizovan', 'jelena', 'ivana', 'Nega kose', 'Dubinska hidratacija i feniranje.', null, 'Kerastase', 'Hydra maska 20 min + serum', 'gotovina', 'placeno'],
            [-21, '14:00', '16:30', 'realizovan', 'marija', 'ivana', 'Balayage', 'Balayage i hladni toner.', '9.1', 'Wella Professionals', 'Blondor + toner 9.1', 'kartica', 'placeno'],
            [-18, '10:00', '10:35', 'realizovan', 'nikola', 'marko', 'Muško šišanje', 'Kratko šišanje sa fade prelazom.', null, 'Reuzel', 'Mat pasta za teksturu', 'gotovina', 'placeno'],
            [-14, '17:00', '18:30', 'realizovan', 'tamara', 'mila', 'Keratin', 'Keratin tretman za zaglađivanje.', null, 'Cocochoco', 'Keratin classic tretman', 'kartica', 'placeno'],
            [-10, '12:00', '13:30', 'otkazan', 'sofija', 'stefan', 'Svečana frizura', null, null, null, null, null, 'neplaceno'],
            [-7, '15:00', '17:30', 'realizovan', 'milica', 'ivana', 'Pramenovi', 'Pramenovi oko lica i gloss.', '8.13', 'Schwarzkopf', 'Igora 8.13 gloss', 'kartica', 'placeno'],
            [-3, '09:30', '10:10', 'realizovan', 'lazar', 'marko', 'Oblikovanje brade', 'Konturisanje brade i topli peškir.', null, 'Reuzel', 'Balzam i ulje za bradu', 'gotovina', 'placeno'],
            [1, '10:00', '10:45', 'zakazan', 'sanja', 'mila', 'Šišanje', null, null, null, null, null, 'neplaceno'],
            [1, '12:00', '13:00', 'zakazan', 'katarina', 'stefan', 'Nega kose', null, null, null, null, null, 'neplaceno'],
            [2, '16:00', '18:00', 'zakazan', 'ana', 'mila', 'Farbanje', null, null, null, null, null, 'neplaceno'],
            [3, '11:00', '12:30', 'zakazan', 'jelena', 'ivana', 'Feniranje', null, null, null, null, null, 'neplaceno'],
            [4, '13:00', '17:00', 'zakazan', 'marija', 'ivana', 'AirTouch', null, null, null, null, null, 'neplaceno'],
            [5, '09:00', '09:35', 'zakazan', 'nikola', 'marko', 'Muško šišanje', null, null, null, null, null, 'neplaceno'],
            [6, '18:00', '19:30', 'zakazan', 'tamara', 'mila', 'Botox kose', null, null, null, null, null, 'neplaceno'],
            [8, '10:00', '11:30', 'zakazan', 'sofija', 'stefan', 'Svečana frizura', null, null, null, null, null, 'neplaceno'],
            [9, '14:00', '16:30', 'zakazan', 'milica', 'ivana', 'Pramenovi', null, null, null, null, null, 'neplaceno'],
            [11, '10:30', '11:15', 'zakazan', 'lazar', 'marko', 'Muško šišanje', null, null, null, null, null, 'neplaceno'],
            [12, '12:00', '12:20', 'zakazan', 'katarina', 'mila', 'Konsultacije', null, null, null, null, null, 'neplaceno'],
            [14, '16:00', '17:00', 'zakazan', 'sanja', 'stefan', 'Feniranje', null, null, null, null, null, 'neplaceno'],
        ];

        foreach ($termini as [$offset, $start, $end, $status, $klijentKey, $zaposleniKey, $uslugaName, $opisTretmana, $nijansa, $proizvodjac, $formula, $placanje, $statusPlacanja]) {
            $date = Carbon::today()->addDays($offset)->format('Y-m-d');
            $termin = $this->updateOrCreateTermin(
                $date,
                $start,
                $klijenti[$klijentKey]->id,
                $zaposleni[$zaposleniKey]->id,
                [
                    'vreme_zavrsetka' => $this->normalizeTime($end),
                    'status' => $status,
                    'napomena' => $this->terminNapomena($status, $uslugaName),
                    'usluga_id' => $usluge[$uslugaName]->id,
                ],
            );

            if ($status === 'realizovan') {
                $this->seedTretmanIRacun($termin, $opisTretmana, $nijansa, $proizvodjac, $formula, $placanje, $statusPlacanja);
            }

            if ($status === 'zakazan') {
                $this->seedPodsetnik($termin);
            }
        }
    }

    private function updateOrCreateTermin(
        string $date,
        string $start,
        int $klijentId,
        int $zaposleniId,
        array $values,
    ): Termin {
        $startTime = $this->normalizeTime($start);

        $termin = Termin::query()
            ->whereDate('datum', $date)
            ->where('klijent_id', $klijentId)
            ->where('zaposleni_id', $zaposleniId)
            ->where(function ($query) use ($start, $startTime) {
                $query->where('vreme_pocetka', $start)
                    ->orWhere('vreme_pocetka', $startTime);
            })
            ->first();

        if (! $termin) {
            $termin = new Termin([
                'datum' => $date,
                'vreme_pocetka' => $startTime,
                'klijent_id' => $klijentId,
                'zaposleni_id' => $zaposleniId,
            ]);
        }

        $termin->fill($values);
        $termin->save();

        return $termin;
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time.':00' : $time;
    }

    private function seedTretmanIRacun(
        Termin $termin,
        ?string $opisTretmana,
        ?string $nijansa,
        ?string $proizvodjac,
        ?string $formula,
        ?string $placanje,
        string $statusPlacanja,
    ): void {
        $tretman = EvidencijaTretmana::updateOrCreate(
            ['termin_id' => $termin->id],
            [
                'datum' => $termin->datum,
                'opis_tretmana' => $opisTretmana ?: 'Realizovan salonski tretman.',
                'nijansa' => $nijansa,
                'proizvodjac' => $proizvodjac,
                'formula' => $formula,
                'korisceni_preparati' => 'Šampon za pripremu, zaštitni serum, maska za završnu negu.',
                'napomena' => 'Preporučena kontrola i sledeći termin po potrebi.',
            ],
        );

        FotografijaTretmana::updateOrCreate(
            [
                'evidencija_tretmana_id' => $tretman->id,
                'naziv' => 'Primer pre tretmana',
            ],
            [
                'putanja' => 'images/salon-cut.jpg',
                'tip_fotografije' => 'pre',
                'datum_dodavanja' => Carbon::parse($termin->datum)->setTime(9, 0),
                'opis' => 'Demo fotografija za pregled istorije tretmana.',
            ],
        );

        FotografijaTretmana::updateOrCreate(
            [
                'evidencija_tretmana_id' => $tretman->id,
                'naziv' => 'Primer posle tretmana',
            ],
            [
                'putanja' => 'images/salon-wash.jpg',
                'tip_fotografije' => 'posle',
                'datum_dodavanja' => Carbon::parse($termin->datum)->setTime(18, 0),
                'opis' => 'Demo fotografija posle usluge.',
            ],
        );

        $racun = Racun::updateOrCreate(
            ['termin_id' => $termin->id],
            [
                'datum_izdavanja' => $termin->datum,
                'ukupan_iznos' => $termin->usluga->cena,
                'nacin_placanja' => $placanje,
                'status_placanja' => $statusPlacanja,
            ],
        );

        if ($statusPlacanja === 'placeno') {
            Uplata::updateOrCreate(
                [
                    'racun_id' => $racun->id,
                    'datum_uplate' => $racun->datum_izdavanja,
                ],
                [
                    'iznos' => $racun->ukupan_iznos,
                    'status_transakcije' => 'uspesno',
                ],
            );
        }
    }

    private function seedPodsetnik(Termin $termin): void
    {
        Podsetnik::updateOrCreate(
            [
                'klijent_id' => $termin->klijent_id,
                'termin_id' => $termin->id,
            ],
            [
                'datum_slanja' => Carbon::parse($termin->datum)->subDay()->setTime(10, 0),
                'tip_podsetnika' => 'email',
                'sadrzaj' => 'Podsetnik za zakazani termin: '.$termin->usluga->naziv.' u '.substr($termin->vreme_pocetka, 0, 5).'.',
                'status' => 'planiran',
            ],
        );
    }

    private function terminNapomena(string $status, string $uslugaName): ?string
    {
        return match ($status) {
            'realizovan' => 'Termin realizovan: '.$uslugaName.'.',
            'otkazan' => 'Klijent je otkazao termin dan ranije.',
            default => 'Zakazan termin za uslugu: '.$uslugaName.'.',
        };
    }
}
