import { CommonModule } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DostupanTermin, Usluga, Zaposleni } from '../models';
import { BookingService } from '../services/booking.service';

@Component({
    selector: 'termin-create-component',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
        <section class="angular-page angular-page--wide">
            <header class="angular-heading">
                <p class="eyebrow">Proces 2</p>
                <h1>Zakazivanje termina</h1>
                <p>Izaberite uslugu, zaposlenog, datum i jedan od slobodnih termina.</p>
            </header>

            <form class="angular-card angular-wide" (ngSubmit)="submit()">
                <div class="alert alert-success" *ngIf="success">{{ success }}</div>
                <div class="alert alert-danger" *ngIf="errors.length">
                    <div *ngFor="let error of errors">{{ error }}</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="zaposleni">Zaposleni</label>
                        <select id="zaposleni" class="form-select" name="zaposleni_id" [(ngModel)]="form.zaposleni_id" (ngModelChange)="loadSlots()" required>
                            <option [ngValue]="null">Izaberite zaposlenog</option>
                            <option *ngFor="let item of zaposleni" [ngValue]="item.id">{{ item.ime }} {{ item.prezime }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="usluga">Usluga</label>
                        <select id="usluga" class="form-select" name="usluga_id" [(ngModel)]="form.usluga_id" (ngModelChange)="loadSlots()" required>
                            <option [ngValue]="null">Izaberite uslugu</option>
                            <option *ngFor="let usluga of usluge" [ngValue]="usluga.id">{{ usluga.naziv }} ({{ usluga.trajanje_minuta }} min)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="datum">Datum</label>
                        <input id="datum" class="form-control" type="date" name="datum" [(ngModel)]="form.datum" [min]="today" (ngModelChange)="loadSlots()" required>
                    </div>
                    <div class="col-md-6">
                        <label for="vreme">Dostupno vreme</label>
                        <select id="vreme" class="form-select" name="vreme_pocetka" [(ngModel)]="form.vreme_pocetka" [disabled]="timeSelectDisabled" required>
                            <option [ngValue]="''">{{ timePlaceholder }}</option>
                            <option *ngFor="let slot of slots" [ngValue]="slot.vreme_pocetka">{{ slot.vreme_pocetka }} - {{ slot.vreme_zavrsetka }}</option>
                        </select>
                        <div class="form-text" *ngIf="timeHelpText">{{ timeHelpText }}</div>
                    </div>
                    <div class="col-12">
                        <label for="napomena">Napomena</label>
                        <input id="napomena" class="form-control" name="napomena" [(ngModel)]="form.napomena" maxlength="1000">
                    </div>
                </div>

                <button class="btn btn-primary mt-3" [disabled]="loading || !form.vreme_pocetka">
                    {{ loading ? 'Zakazivanje...' : 'Zakaži termin' }}
                </button>
            </form>
        </section>
    `,
})
export class TerminCreateComponent implements OnInit {
    private readonly booking = inject(BookingService);
    usluge: Usluga[] = [];
    zaposleni: Zaposleni[] = [];
    slots: DostupanTermin[] = [];
    today = this.localDate();
    loading = false;
    loadingSlots = false;
    slotsLoaded = false;
    errors: string[] = [];
    success = '';
    form = {
        zaposleni_id: null as number | null,
        usluga_id: null as number | null,
        datum: this.localDate(),
        vreme_pocetka: '',
        napomena: '',
    };

    ngOnInit(): void {
        this.booking.options().subscribe({
            next: ({ data }) => {
                this.usluge = data.usluge;
                this.zaposleni = data.zaposleni;
            },
            error: (error) => this.errors = this.errorMessages(error, 'Opcije za zakazivanje nisu učitane.'),
        });
    }

    get canLoadSlots(): boolean {
        return Boolean(this.form.datum && this.form.zaposleni_id && this.form.usluga_id);
    }

    get timeSelectDisabled(): boolean {
        return ! this.canLoadSlots || this.loadingSlots || ! this.slots.length;
    }

    get timePlaceholder(): string {
        if (! this.canLoadSlots) return 'Prvo izaberite zaposlenog, uslugu i datum';
        if (this.loadingSlots) return 'Učitavanje termina...';
        if (this.slotsLoaded && ! this.slots.length) return 'Nema slobodnih termina';
        return 'Izaberite vreme';
    }

    get timeHelpText(): string {
        if (! this.canLoadSlots) return 'Vreme se prikazuje nakon izbora svih obaveznih podataka.';
        if (this.loadingSlots) return 'Proveravam slobodne termine u radnom vremenu 08:00–20:00.';
        if (this.slotsLoaded && ! this.slots.length) return 'Izaberite drugi datum, zaposlenog ili uslugu.';
        return '';
    }

    loadSlots(): void {
        this.errors = [];
        this.success = '';
        this.form.vreme_pocetka = '';
        this.slotsLoaded = false;

        if (! this.canLoadSlots) {
            this.slots = [];
            return;
        }

        this.loadingSlots = true;
        this.booking.availableSlots(this.form.datum, this.form.zaposleni_id!, this.form.usluga_id!)
            .subscribe({
                next: ({ data }) => {
                    this.slots = data;
                    this.slotsLoaded = true;
                },
                error: (error) => {
                    this.slots = [];
                    this.slotsLoaded = true;
                    this.loadingSlots = false;
                    this.errors = this.errorMessages(error, 'Dostupni termini nisu učitani.');
                },
                complete: () => this.loadingSlots = false,
            });
    }

    submit(): void {
        this.loading = true;
        this.errors = [];
        this.success = '';

        if (! this.form.zaposleni_id || ! this.form.usluga_id || ! this.form.vreme_pocetka) {
            this.errors = ['Popunite sva obavezna polja i izaberite dostupno vreme.'];
            this.loading = false;
            return;
        }

        this.booking.create({
            datum: this.form.datum,
            vreme_pocetka: this.form.vreme_pocetka,
            zaposleni_id: this.form.zaposleni_id,
            usluga_id: this.form.usluga_id,
            napomena: this.form.napomena || undefined,
        }).subscribe({
            next: () => {
                this.form.vreme_pocetka = '';
                this.loadSlots();
                this.success = 'Termin je uspešno zakazan.';
            },
            error: (error) => {
                this.errors = this.errorMessages(error, 'Termin nije zakazan.');
                this.loading = false;
            },
            complete: () => this.loading = false,
        });
    }

    private errorMessages(error: any, fallback: string): string[] {
        const bag = error.error?.errors || {};
        const messages = Object.values(bag).flat() as string[];
        return messages.length ? messages : [error.error?.message || fallback];
    }

    private localDate(date = new Date()): string {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
}
