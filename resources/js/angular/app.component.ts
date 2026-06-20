import { CommonModule } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { LoginComponent } from './components/login.component';
import { RegisterComponent } from './components/register.component';
import { TerminCreateComponent } from './components/termin-create.component';
import { AuthService } from './services/auth.service';

type AngularPage = 'login' | 'register' | 'termin-create' | 'redirect';

declare global {
    interface Window {
        salonAngular?: {
            token?: string;
            user?: import('./models').User;
        };
        salonRoutes?: {
            phpBase?: string;
            angularBase?: string;
        };
    }
}

@Component({
    selector: 'app-root',
    standalone: true,
    imports: [
        CommonModule,
        LoginComponent,
        RegisterComponent,
        TerminCreateComponent,
    ],
    template: `
        <section class="angular-app">
            <ng-container *ngIf="page === 'login'">
                <div class="angular-heading">
                    <div>
                        <p class="home-eyebrow">Angular prijava</p>
                        <h1>Prijava korisnika</h1>
                    </div>
                </div>
                <div class="angular-auth-grid angular-auth-grid--single">
                    <login-component [sessionMode]="true"></login-component>
                </div>
            </ng-container>

            <ng-container *ngIf="page === 'register'">
                <div class="angular-heading">
                    <div>
                        <p class="home-eyebrow">Angular registracija</p>
                        <h1>Registracija klijenta</h1>
                    </div>
                </div>
                <div class="angular-auth-grid angular-auth-grid--single">
                    <register-component [sessionMode]="true"></register-component>
                </div>
            </ng-container>

            <ng-container *ngIf="page === 'termin-create'">
                <div class="angular-heading">
                    <div>
                        <p class="home-eyebrow">Angular zakazivanje</p>
                        <h1>Zakazivanje termina</h1>
                    </div>
                </div>
                <termin-create-component *ngIf="auth.user() as user" [user]="user" (created)="afterTerminCreated()"></termin-create-component>
            </ng-container>
        </section>
    `,
})
export class AppComponent implements OnInit {
    readonly auth = inject(AuthService);
    readonly page: AngularPage = this.resolvePage();

    ngOnInit(): void {
        if (this.page === 'redirect') {
            this.redirectToPhp();
            return;
        }

        if (window.salonAngular?.token && window.salonAngular.user) {
            this.auth.setInitialSession(window.salonAngular.token, window.salonAngular.user);
            return;
        }

        if (this.page === 'termin-create' && ! this.auth.token()) {
            this.bootstrapTerminSession();
            return;
        }

        if (this.auth.token()) {
            this.auth.loadUser().subscribe({
                error: () => {
                    this.auth.clearSession();

                    if (this.page === 'termin-create') {
                        this.bootstrapTerminSession();
                    }
                },
            });
        }
    }

    afterTerminCreated(): void {
        window.location.href = this.phpUrl('/termini');
    }

    private resolvePage(): AngularPage {
        const explicitPage = document.querySelector<HTMLElement>('app-root')?.dataset['page'] as AngularPage | undefined;

        if (explicitPage) {
            return explicitPage;
        }

        return this.pageFromPath(window.location.pathname);
    }

    private pageFromPath(path: string): AngularPage {
        if (path === '/login') {
            return 'login';
        }

        if (path === '/register') {
            return 'register';
        }

        if (path === '/termini/create') {
            return 'termin-create';
        }

        return 'redirect';
    }

    private bootstrapTerminSession(): void {
        this.auth.bootstrapFromLaravelSession().subscribe({
            error: () => window.location.href = this.angularUrl('/login'),
        });
    }

    private redirectToPhp(): void {
        window.location.href = this.phpUrl(`${window.location.pathname}${window.location.search}`);
    }

    private phpUrl(path: string): string {
        return `${this.baseUrl(window.salonRoutes?.phpBase, window.location.origin)}${this.pathWithSlash(path)}`;
    }

    private angularUrl(path: string): string {
        return `${this.baseUrl(window.salonRoutes?.angularBase, window.location.origin)}${this.pathWithSlash(path)}`;
    }

    private baseUrl(value: string | undefined, fallback: string): string {
        return (value || fallback).replace(/\/$/, '');
    }

    private pathWithSlash(path: string): string {
        return path.startsWith('/') ? path : `/${path}`;
    }
}
