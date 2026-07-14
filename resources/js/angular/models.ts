export interface Klijent {
    id: number;
    ime: string;
    prezime: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'administrator' | 'zaposleni' | 'klijent';
    klijent?: Klijent | null;
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

export interface Zaposleni {
    id: number;
    ime: string;
    prezime: string;
}

export interface Usluga {
    id: number;
    naziv: string;
    tip_usluge: string;
    trajanje_minuta: number;
    cena: string | number;
}

export interface BookingOptions {
    usluge: Usluga[];
    zaposleni: Zaposleni[];
}

export interface DostupanTermin {
    datum: string;
    vreme_pocetka: string;
    vreme_zavrsetka: string;
}

export interface BookingPayload {
    datum: string;
    vreme_pocetka: string;
    zaposleni_id: number;
    usluga_id: number;
    napomena?: string;
}

export interface Termin extends BookingPayload {
    id: number;
    vreme_zavrsetka: string;
    status: 'zakazan';
    klijent_id: number;
}
