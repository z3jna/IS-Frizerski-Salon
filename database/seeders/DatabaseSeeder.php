<?php

namespace Database\Seeders;

use App\Models\EvidencijaTretmana;
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
        $admin = User::updateOrCreate(
            ['email' => 'admin@salon.test'],
            [
                'name' => 'Administrator Salona',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ],
        );

        $zaposleniUser = User::updateOrCreate(
            ['email' => 'mila@salon.test'],
            [
                'name' => 'Mila Petrovic',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ZAPOSLENI,
            ],
        );

        $zaposleni = Zaposleni::updateOrCreate(
            ['user_id' => $zaposleniUser->id],
            [
                'ime' => 'Mila',
                'prezime' => 'Petrovic',
                'telefon' => '060/111-222',
                'pozicija' => 'Senior frizer',
                'radno_vreme' => 'Pon-Pet 09:00-17:00',
                'datum_zaposlenja' => '2022-03-15',
                'plata' => 95000,
            ],
        );

        $klijentUser = User::updateOrCreate(
            ['email' => 'ana@salon.test'],
            [
                'name' => 'Ana Jovanovic',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KLIJENT,
            ],
        );

        $klijent = Klijent::updateOrCreate(
            ['user_id' => $klijentUser->id],
            [
                'ime' => 'Ana',
                'prezime' => 'Jovanovic',
                'telefon' => '061/333-444',
                'adresa' => 'Bulevar umetnosti 12',
                'datum_rodjenja' => '1993-08-21',
                'napomena' => 'Osetljivo teme, izbegavati agresivne preparate.',
                'preferencije' => 'Topli tonovi, zakazivanje posle 16h.',
            ],
        );

        $usluge = collect([
            ['naziv' => 'Sisanje', 'tip_usluge' => 'Osnovna usluga', 'opis' => 'Zensko sisanje sa konsultacijom.', 'trajanje_minuta' => 45, 'cena' => 2500],
            ['naziv' => 'Feniranje', 'tip_usluge' => 'Stilizovanje', 'opis' => 'Pranje i feniranje kose.', 'trajanje_minuta' => 40, 'cena' => 1800],
            ['naziv' => 'Farbanje', 'tip_usluge' => 'Koloracija', 'opis' => 'Farbanje izrastka ili cele duzine.', 'trajanje_minuta' => 120, 'cena' => 6000],
            ['naziv' => 'Balayage', 'tip_usluge' => 'Koloracija', 'opis' => 'Balayage tehnika sa toniranjem.', 'trajanje_minuta' => 180, 'cena' => 12000],
            ['naziv' => 'Pramenovi', 'tip_usluge' => 'Koloracija', 'opis' => 'Klasicni pramenovi sa folijom.', 'trajanje_minuta' => 150, 'cena' => 9000],
            ['naziv' => 'Keratin', 'tip_usluge' => 'Nega kose', 'opis' => 'Keratin tretman za zagladjivanje kose.', 'trajanje_minuta' => 150, 'cena' => 10000],
            ['naziv' => 'Nega kose', 'tip_usluge' => 'Nega kose', 'opis' => 'Dubinska hidratacija i obnova.', 'trajanje_minuta' => 60, 'cena' => 3500],
            ['naziv' => 'Pakovanje', 'tip_usluge' => 'Nega kose', 'opis' => 'Brzo salonsko pakovanje posle pranja.', 'trajanje_minuta' => 30, 'cena' => 1500],
        ])->map(fn (array $data) => Usluga::updateOrCreate(
            ['naziv' => $data['naziv']],
            $data + ['dostupnost' => true],
        ));

        $farbanje = $usluge->firstWhere('naziv', 'Farbanje');

        $termin = Termin::updateOrCreate(
            [
                'datum' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'vreme_pocetka' => '16:00:00',
                'klijent_id' => $klijent->id,
                'zaposleni_id' => $zaposleni->id,
            ],
            [
                'vreme_zavrsetka' => '18:00:00',
                'status' => 'zakazan',
                'napomena' => 'Klijent zeli konsultaciju o nijansi.',
                'usluga_id' => $farbanje->id,
            ],
        );

        $realizovanTermin = Termin::updateOrCreate(
            [
                'datum' => Carbon::today()->subWeeks(4)->format('Y-m-d'),
                'vreme_pocetka' => '16:00:00',
                'klijent_id' => $klijent->id,
                'zaposleni_id' => $zaposleni->id,
            ],
            [
                'vreme_zavrsetka' => '18:00:00',
                'status' => 'realizovan',
                'napomena' => 'Prethodna koloracija.',
                'usluga_id' => $farbanje->id,
            ],
        );

        $tretman = EvidencijaTretmana::updateOrCreate(
            ['termin_id' => $realizovanTermin->id],
            [
                'datum' => $realizovanTermin->datum,
                'opis_tretmana' => 'Osvezen izrastak i toniranje duzine.',
                'nijansa' => '7.34',
                'proizvodjac' => 'L Oreal Professionnel',
                'formula' => '7.34 + 6% oksidant, odnos 1:1.5',
                'korisceni_preparati' => 'Zastitni serum, kolor sampon, maska za farbanu kosu.',
                'napomena' => 'Sledece farbanje preporuceno za 5 nedelja.',
            ],
        );

        $racun = Racun::updateOrCreate(
            ['termin_id' => $realizovanTermin->id],
            [
                'datum_izdavanja' => Carbon::today()->subWeeks(4)->format('Y-m-d'),
                'ukupan_iznos' => $farbanje->cena,
                'nacin_placanja' => 'kartica',
                'status_placanja' => 'placeno',
            ],
        );

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

        Podsetnik::updateOrCreate(
            [
                'klijent_id' => $klijent->id,
                'termin_id' => $termin->id,
            ],
            [
                'datum_slanja' => Carbon::today()->addDays(2)->setTime(10, 0),
                'tip_podsetnika' => 'email',
                'sadrzaj' => 'Podsetnik za zakazani termin i preporuka da se farbanje ponavlja na 4-6 nedelja.',
                'status' => 'planiran',
            ],
        );
    }
}
