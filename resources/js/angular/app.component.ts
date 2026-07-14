import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService } from './services/auth.service';

@Component({
    selector: 'app-root',
    standalone: true,
    imports: [CommonModule, RouterLink, RouterLinkActive, RouterOutlet],
    template: `
        <nav class="navbar navbar-expand-xl navbar-dark app-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Frizerski salon</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#angular-nav" aria-controls="angular-nav" aria-expanded="false" aria-label="Otvori navigaciju">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="angular-nav">
                    <ng-container *ngIf="auth.user() as user; else guestLinks">
                        <ul class="navbar-nav me-auto mb-2 mb-xl-0">
                            <li class="nav-item"><a class="nav-link" href="/">Početna</a></li>
                            <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="/usluge">Usluge</a></li>
                            <li class="nav-item"><a class="nav-link" href="/termini">Termini</a></li>
                            <li class="nav-item"><a class="nav-link" routerLink="/termini/create" routerLinkActive="active">Zakazivanje</a></li>
                            <li class="nav-item"><a class="nav-link" href="/tretmani">Tretmani</a></li>
                            <li class="nav-item"><a class="nav-link" href="/racuni">Računi</a></li>
                            <li class="nav-item"><a class="nav-link" href="/podsetnici">Podsetnici</a></li>
                            <li class="nav-item" *ngIf="user.role === 'administrator' || user.role === 'zaposleni'"><a class="nav-link" href="/klijenti">Klijenti</a></li>
                            <ng-container *ngIf="user.role === 'administrator'">
                                <li class="nav-item"><a class="nav-link" href="/zaposleni">Zaposleni</a></li>
                                <li class="nav-item"><a class="nav-link" href="/uplate">Uplate</a></li>
                                <li class="nav-item"><a class="nav-link" href="/izvestaji">Izveštaji</a></li>
                            </ng-container>
                        </ul>
                        <div class="navbar-account">
                            <span class="navbar-account__label">{{ user.name }}</span>
                            <button type="button" class="btn btn-sm btn-outline-light" (click)="logout()">Odjava</button>
                        </div>
                    </ng-container>
                    <ng-template #guestLinks>
                        <ul class="navbar-nav ms-auto align-items-xl-center">
                            <li class="nav-item"><a class="nav-link" href="/">Početna</a></li>
                            <li class="nav-item"><a class="nav-link" routerLink="/login" routerLinkActive="active">Prijava</a></li>
                            <li class="nav-item"><a class="btn btn-sm btn-light ms-xl-2" routerLink="/register">Registracija</a></li>
                        </ul>
                    </ng-template>
                </div>
            </div>
        </nav>

        <main class="app-shell">
            <div class="container-fluid px-4">
                <router-outlet></router-outlet>
            </div>
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
