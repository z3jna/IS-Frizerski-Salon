export interface User {
    id: number;
    name: string;
    email: string;
    role: 'administrator' | 'zaposleni' | 'klijent';
    klijent?: Klijent | null;
    zaposleni?: Zaposleni | null;
}

export interface AuthResponse {
    message: string;
    token: string;
    user: User;
}

export interface ApiCollection<T> {
    data: T[];
}

export interface ApiItem<T> {
    message?: string;
    data: T;
}

export interface Klijent {
    id: number;
    ime: string;
    prezime: string;
    telefon?: string | null;
    adresa?: string | null;
    datum_rodjenja?: string | null;
}

export interface Zaposleni {
    id: number;
    ime: string;
    prezime: string;
    pozicija?: string | null;
    radno_vreme?: string | null;
}

export interface Usluga {
    id: number;
    naziv: string;
    tip_usluge: string;
    opis?: string | null;
    trajanje_minuta: number;
    cena: string | number;
    dostupnost: boolean;
}

export interface Termin {
    id: number;
    datum: string;
    vreme_pocetka: string;
    vreme_zavrsetka: string;
    status: 'zakazan' | 'realizovan' | 'otkazan';
    napomena?: string | null;
    klijent_id: number;
    zaposleni_id: number;
    usluga_id: number;
    klijent?: Klijent;
    zaposleni?: Zaposleni;
    usluga?: Usluga;
}

export interface DostupanTermin {
    datum: string;
    vreme_pocetka: string;
    vreme_zavrsetka: string;
}

export interface TretmanPayload {
    termin_id: number | null;
    datum: string;
    opis_tretmana: string;
    nijansa?: string;
    proizvodjac?: string;
    formula?: string;
    korisceni_preparati?: string;
    napomena?: string;
}

export interface Racun {
    id: number;
    datum_izdavanja: string;
    ukupan_iznos: string | number;
    nacin_placanja?: string | null;
    status_placanja: string;
    termin?: Termin;
}
