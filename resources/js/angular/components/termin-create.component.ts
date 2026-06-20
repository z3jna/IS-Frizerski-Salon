import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DostupanTermin, Klijent, Usluga, User, Zaposleni } from '../models';
import { KlijentiService } from '../services/klijenti.service';
import { TerminiService } from '../services/termini.service';
import { UslugeService } from '../services/usluge.service';
import { ZaposleniService } from '../services/zaposleni.service';

@Component({
    selector: 'termin-create-component',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
        <form class="angular-card angular-wide" (ngSubmit)="submit()">
            <h2>Zakazivanje termina</h2>
            <div class="alert alert-danger" *ngIf="errors.length">
                <div *ngFor="let error of errors">{{ error }}</div>
            </div>
            <div class="row g-2">
                <div class="col-md-4" *ngIf="user?.role !== 'klijent'">
                    <label>Klijent</label>
                    <select class="form-select" name="klijent_id" [(ngModel)]="form.klijent_id">
                        <option [ngValue]="null">Izaberi klijenta</option>
                        <option *ngFor="let klijent of klijenti" [ngValue]="klijent.id">{{ klijent.ime }} {{ klijent.prezime }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Zaposleni</label>
                    <select class="form-select" name="zaposleni_id" [(ngModel)]="form.zaposleni_id" (ngModelChange)="loadSlots()">
                        <option [ngValue]="null">Izaberi zaposlenog</option>
                        <option *ngFor="let item of zaposleni" [ngValue]="item.id">{{ item.ime }} {{ item.prezime }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Usluga</label>
                    <select class="form-select" name="usluga_id" [(ngModel)]="form.usluga_id" (ngModelChange)="loadSlots()">
                        <option [ngValue]="null">Izaberi uslugu</option>
                        <option *ngFor="let usluga of usluge" [ngValue]="usluga.id">{{ usluga.naziv }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Datum</label>
                    <input class="form-control" type="date" name="datum" [(ngModel)]="form.datum" [min]="today" (ngModelChange)="loadSlots()" required>
                </div>
                <div class="col-md-4">
                    <label>Dostupno vreme</label>
                    <select class="form-select" name="vreme_pocetka" [(ngModel)]="form.vreme_pocetka" [disabled]="timeSelectDisabled" required>
                        <option [ngValue]="''">{{ timePlaceholder }}</option>
                        <option *ngFor="let slot of slots" [ngValue]="slot.vreme_pocetka">{{ slot.vreme_pocetka }} - {{ slot.vreme_zavrsetka }}</option>
                    </select>
                    <div class="form-text" *ngIf="timeHelpText">{{ timeHelpText }}</div>
                </div>
                <div class="col-md-8">
                    <label>Napomena</label>
                    <input class="form-control" name="napomena" [(ngModel)]="form.napomena">
                </div>
            </div>
            <button class="btn btn-primary mt-3" [disabled]="loading">{{ loading ? 'Zakazivanje...' : 'Zakazi termin' }}</button>
        </form>
    `,
})
export class TerminCreateComponent implements OnInit {
    private readonly terminiService = inject(TerminiService);
    private readonly klijentiService = inject(KlijentiService);
    private readonly zaposleniService = inject(ZaposleniService);
    private readonly uslugeService = inject(UslugeService);
    @Input() user: User | null = null;
    @Output() created = new EventEmitter<void>();
    klijenti: Klijent[] = [];
    zaposleni: Zaposleni[] = [];
    usluge: Usluga[] = [];
    slots: DostupanTermin[] = [];
    today = this.localDate();
    loading = false;
    loadingSlots = false;
    slotsLoaded = false;
    errors: string[] = [];
    form = {
        klijent_id: null as number | null,
        zaposleni_id: null as number | null,
        usluga_id: null as number | null,
        datum: this.localDate(),
        vreme_pocetka: '',
        napomena: '',
    };

    ngOnInit(): void {
        this.uslugeService.all().subscribe((response) => this.usluge = response.data);
        this.zaposleniService.all().subscribe((response) => this.zaposleni = response.data);
        if (this.user?.role !== 'klijent') {
            this.klijentiService.all().subscribe((response) => this.klijenti = response.data);
        }
    }

    get canLoadSlots(): boolean {
        return Boolean(this.form.datum && this.form.zaposleni_id && this.form.usluga_id);
    }

    get timeSelectDisabled(): boolean {
        return ! this.canLoadSlots || this.loadingSlots || ! this.slots.length;
    }

    get timePlaceholder(): string {
        if (! this.canLoadSlots) {
            return 'Prvo izaberi zaposlenog, uslugu i datum';
        }

        if (this.loadingSlots) {
            return 'Ucitavanje termina...';
        }

        if (this.slotsLoaded && ! this.slots.length) {
            return 'Nema slobodnih termina';
        }

        return 'Izaberi vreme';
    }

    get timeHelpText(): string {
        if (! this.canLoadSlots) {
            return 'Dostupna vremena se prikazuju tek nakon izbora zaposlenog, usluge i datuma.';
        }

        if (this.loadingSlots) {
            return 'Proveravam slobodne termine u radnom vremenu 08:00-20:00.';
        }

        if (this.slotsLoaded && ! this.slots.length) {
            return 'Za izabrani datum nema slobodnih termina u buducnosti. Probajte drugi datum, zaposlenog ili uslugu.';
        }

        return '';
    }

    loadSlots(): void {
        this.errors = [];
        this.form.vreme_pocetka = '';
        this.slotsLoaded = false;

        if (! this.canLoadSlots) {
            this.slots = [];
            this.loadingSlots = false;
            return;
        }

        this.loadingSlots = true;
        this.terminiService.dostupniTermini(this.form.datum, this.form.zaposleni_id, this.form.usluga_id)
            .subscribe({
                next: (response) => {
                    this.slots = response.data;
                    this.slotsLoaded = true;
                },
                error: (error) => {
                    this.slots = [];
                    this.slotsLoaded = true;
                    this.loadingSlots = false;
                    const bag = error.error?.errors || {};
                    this.errors = Object.values(bag).flat() as string[];
                    if (! this.errors.length) {
                        this.errors = [error.error?.message || 'Nema dostupnih termina za izabrani datum.'];
                    }
                },
                complete: () => this.loadingSlots = false,
            });
    }

    submit(): void {
        this.loading = true;
        this.errors = [];

        if (! this.form.vreme_pocetka) {
            this.errors = ['Izaberite dostupno vreme termina.'];
            this.loading = false;
            return;
        }

        const payload: Record<string, unknown> = { ...this.form };
        if (this.user?.role === 'klijent') {
            delete payload['klijent_id'];
        }

        this.terminiService.create(payload).subscribe({
            next: () => {
                this.created.emit();
                this.form.vreme_pocetka = '';
                this.loadSlots();
            },
            error: (error) => {
                const bag = error.error?.errors || {};
                this.errors = Object.values(bag).flat() as string[];
                if (! this.errors.length) {
                    this.errors = [error.error?.message || 'Termin nije zakazan.'];
                }
                this.loading = false;
            },
            complete: () => this.loading = false,
        });
    }

    private localDate(date = new Date()): string {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }
}
