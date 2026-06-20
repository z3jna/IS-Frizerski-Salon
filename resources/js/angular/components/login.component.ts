import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../services/auth.service';

@Component({
    selector: 'login-component',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
        <form class="angular-card" (ngSubmit)="submit()">
            <h2>Prijava</h2>
            <div class="alert alert-danger" *ngIf="error">{{ error }}</div>
            <label>Email</label>
            <input class="form-control" type="email" name="email" [(ngModel)]="email" required>
            <label>Lozinka</label>
            <input class="form-control" type="password" name="password" [(ngModel)]="password" required>
            <button class="btn btn-primary w-100 mt-3" [disabled]="loading">{{ loading ? 'Prijava...' : 'Prijavi se' }}</button>
        </form>
    `,
})
export class LoginComponent {
    private readonly auth = inject(AuthService);
    @Input() sessionMode = false;
    @Output() loggedIn = new EventEmitter<void>();
    email = 'ana@salon.test';
    password = 'password';
    loading = false;
    error = '';

    submit(): void {
        this.loading = true;
        this.error = '';
        const request = this.sessionMode
            ? this.auth.sessionLogin({ email: this.email, password: this.password })
            : this.auth.login({ email: this.email, password: this.password });

        request.subscribe({
            next: (response) => {
                this.loggedIn.emit();
                if (this.sessionMode && 'redirect' in response) {
                    window.location.href = response.redirect;
                }
            },
            error: (error) => {
                this.error = error.error?.message || 'Prijava nije uspela.';
                this.loading = false;
            },
            complete: () => this.loading = false,
        });
    }
}
