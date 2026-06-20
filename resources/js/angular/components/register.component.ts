import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../services/auth.service';

@Component({
    selector: 'register-component',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
        <form class="angular-card" (ngSubmit)="submit()">
            <h2>Registracija klijenta</h2>
            <div class="alert alert-danger" *ngIf="errors.length">
                <div *ngFor="let item of errors">{{ item }}</div>
            </div>
            <div class="row g-2">
                <div class="col-md-6"><label>Ime</label><input class="form-control" name="ime" [(ngModel)]="form.ime" required></div>
                <div class="col-md-6"><label>Prezime</label><input class="form-control" name="prezime" [(ngModel)]="form.prezime" required></div>
                <div class="col-12"><label>Email</label><input class="form-control" type="email" name="email" [(ngModel)]="form.email" required></div>
                <div class="col-md-6"><label>Telefon</label><input class="form-control" name="telefon" [(ngModel)]="form.telefon"></div>
                <div class="col-md-6"><label>Adresa</label><input class="form-control" name="adresa" [(ngModel)]="form.adresa"></div>
                <div class="col-md-6"><label>Lozinka</label><input class="form-control" type="password" name="password" [(ngModel)]="form.password" required></div>
                <div class="col-md-6"><label>Potvrda lozinke</label><input class="form-control" type="password" name="password_confirmation" [(ngModel)]="form.password_confirmation" required></div>
            </div>
            <button class="btn btn-primary w-100 mt-3" [disabled]="loading">{{ loading ? 'Cuvanje...' : 'Registruj se' }}</button>
        </form>
    `,
})
export class RegisterComponent {
    private readonly auth = inject(AuthService);
    @Input() sessionMode = false;
    @Output() registered = new EventEmitter<void>();
    loading = false;
    errors: string[] = [];
    form = {
        ime: '',
        prezime: '',
        email: '',
        telefon: '',
        adresa: '',
        password: '',
        password_confirmation: '',
    };

    submit(): void {
        this.loading = true;
        this.errors = [];
        const request = this.sessionMode ? this.auth.sessionRegister(this.form) : this.auth.register(this.form);

        request.subscribe({
            next: (response) => {
                this.registered.emit();
                if (this.sessionMode && 'redirect' in response) {
                    window.location.href = response.redirect;
                }
            },
            error: (error) => {
                const bag = error.error?.errors || {};
                this.errors = Object.values(bag).flat() as string[];
                if (! this.errors.length) {
                    this.errors = [error.error?.message || 'Registracija nije uspela.'];
                }
                this.loading = false;
            },
            complete: () => this.loading = false,
        });
    }
}
