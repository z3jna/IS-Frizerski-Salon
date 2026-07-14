import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Component({
    selector: 'register-component',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterLink],
    template: `
        <section class="angular-page angular-page--wide">
            <header class="angular-heading">
                <h1>Registracija klijenta</h1>
                <p>Kreirajte klijentski nalog i nastavite na zakazivanje.</p>
            </header>
            <form class="angular-card angular-wide" (ngSubmit)="submit()">
                <div class="alert alert-danger" *ngIf="errors.length">
                    <div *ngFor="let item of errors">{{ item }}</div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6"><label for="register-ime">Ime</label><input id="register-ime" class="form-control" name="ime" [(ngModel)]="form.ime" required></div>
                    <div class="col-md-6"><label for="register-prezime">Prezime</label><input id="register-prezime" class="form-control" name="prezime" [(ngModel)]="form.prezime" required></div>
                    <div class="col-12"><label for="register-email">Email</label><input id="register-email" class="form-control" type="email" name="email" [(ngModel)]="form.email" required></div>
                    <div class="col-md-6"><label for="register-telefon">Telefon</label><input id="register-telefon" class="form-control" name="telefon" [(ngModel)]="form.telefon"></div>
                    <div class="col-md-6"><label for="register-adresa">Adresa</label><input id="register-adresa" class="form-control" name="adresa" [(ngModel)]="form.adresa"></div>
                    <div class="col-md-6"><label for="register-password">Lozinka</label><input id="register-password" class="form-control" type="password" name="password" [(ngModel)]="form.password" minlength="8" required></div>
                    <div class="col-md-6"><label for="register-password-confirmation">Potvrda lozinke</label><input id="register-password-confirmation" class="form-control" type="password" name="password_confirmation" [(ngModel)]="form.password_confirmation" minlength="8" required></div>
                </div>
                <button class="btn btn-primary w-100 mt-3" [disabled]="loading">{{ loading ? 'Čuvanje...' : 'Registruj se' }}</button>
                <p class="form-link">Već imate nalog? <a routerLink="/login">Prijavite se</a>.</p>
            </form>
        </section>
    `,
})
export class RegisterComponent {
    private readonly auth = inject(AuthService);
    private readonly router = inject(Router);
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
        this.auth.register(this.form).subscribe({
            next: () => {
                this.router.navigateByUrl('/termini/create');
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
