import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService } from './services/auth.service';

@Component({
    selector: 'app-root',
    standalone: true,
    imports: [CommonModule, RouterLink, RouterLinkActive, RouterOutlet],
    template: `
        <nav class="app-navbar">
            <a class="navbar-brand" routerLink="/login">Frizerski salon</a>
            <div class="app-nav-links">
                <ng-container *ngIf="auth.user(); else guestLinks">
                    <a routerLink="/termini/create" routerLinkActive="active">Zakazivanje</a>
                    <button type="button" class="btn btn-sm btn-outline-light" (click)="logout()">Odjava</button>
                </ng-container>
                <ng-template #guestLinks>
                    <a routerLink="/login" routerLinkActive="active">Prijava</a>
                    <a routerLink="/register" routerLinkActive="active">Registracija</a>
                </ng-template>
            </div>
        </nav>

        <main class="app-shell">
            <router-outlet></router-outlet>
        </main>
    `,
})
export class AppComponent {
    readonly auth = inject(AuthService);
    private readonly router = inject(Router);

    logout(): void {
        this.auth.logout().subscribe({
            next: () => this.router.navigateByUrl('/login'),
            error: () => {
                this.auth.clearAuth();
                this.router.navigateByUrl('/login');
            },
        });
    }
}
