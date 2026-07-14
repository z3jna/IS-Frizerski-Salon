import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Component({
    selector: 'login-component',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterLink],
    template: `
        <section class="angular-page">
            <header class="angular-heading">
                <p class="eyebrow">Proces 1</p>
                <h1>Prijava klijenta</h1>
                <p>Prijavite se API nalogom da biste zakazali termin.</p>
            </header>
            <form class="angular-card" (ngSubmit)="submit()">
                <div class="alert alert-danger" *ngIf="error">{{ error }}</div>
                <label for="login-email">Email</label>
                <input id="login-email" class="form-control" type="email" name="email" [(ngModel)]="email" required>
                <label for="login-password">Lozinka</label>
                <input id="login-password" class="form-control" type="password" name="password" [(ngModel)]="password" required>
                <button class="btn btn-primary w-100 mt-3" [disabled]="loading">{{ loading ? 'Prijava...' : 'Prijavi se' }}</button>
                <p class="form-link">Nemate nalog? <a routerLink="/register">Registrujte se</a>.</p>
            </form>
        </section>
    `,
})
export class LoginComponent {
    private readonly auth = inject(AuthService);
    private readonly router = inject(Router);
    email = 'ana@salon.test';
    password = 'password';
    loading = false;
    error = '';

    submit(): void {
        this.loading = true;
        this.error = '';
        this.auth.login({ email: this.email, password: this.password }).subscribe({
            next: () => {
                this.router.navigateByUrl('/termini/create');
            },
            error: (error) => {
                this.error = error.error?.message || 'Prijava nije uspela.';
                this.loading = false;
            },
            complete: () => this.loading = false,
        });
    }
}
