import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable, inject, signal } from '@angular/core';
import { Observable, switchMap, tap } from 'rxjs';
import { AuthResponse, User } from '../models';

interface SessionLoginResponse extends AuthResponse {
    redirect: string;
}

interface SessionBootstrapResponse {
    token: string;
    user: User;
}

interface CsrfResponse {
    token: string;
}

@Injectable({ providedIn: 'root' })
export class AuthService {
    private readonly http = inject(HttpClient);
    private readonly tokenKey = 'salon_api_token';
    readonly user = signal<User | null>(null);

    token(): string | null {
        return localStorage.getItem(this.tokenKey);
    }

    login(payload: { email: string; password: string }): Observable<AuthResponse> {
        return this.http.post<AuthResponse>('/api/login', payload).pipe(tap((response) => this.saveSession(response)));
    }

    sessionLogin(payload: { email: string; password: string }): Observable<SessionLoginResponse> {
        return this.csrfToken().pipe(
            switchMap((token) => this.http.post<SessionLoginResponse>('/angular-login', payload, {
                headers: this.sessionHeaders(token),
            })),
            tap((response) => this.saveSession(response)),
        );
    }

    register(payload: Record<string, unknown>): Observable<AuthResponse> {
        return this.http.post<AuthResponse>('/api/register', payload).pipe(tap((response) => this.saveSession(response)));
    }

    sessionRegister(payload: Record<string, unknown>): Observable<SessionLoginResponse> {
        return this.csrfToken().pipe(
            switchMap((token) => this.http.post<SessionLoginResponse>('/angular-register', payload, {
                headers: this.sessionHeaders(token),
            })),
            tap((response) => this.saveSession(response)),
        );
    }

    bootstrapFromLaravelSession(): Observable<SessionBootstrapResponse> {
        return this.http.get<SessionBootstrapResponse>('/angular-session', {
            headers: new HttpHeaders({ Accept: 'application/json' }),
        }).pipe(tap((response) => this.saveSession(response)));
    }

    loadUser(): Observable<{ user: User }> {
        return this.http.get<{ user: User }>('/api/user').pipe(tap((response) => this.user.set(response.user)));
    }

    logout(): Observable<{ message: string }> {
        return this.http.post<{ message: string }>('/api/logout', {}).pipe(tap(() => this.clearSession()));
    }

    clearSession(): void {
        localStorage.removeItem(this.tokenKey);
        this.user.set(null);
    }

    setInitialSession(token: string, user: User): void {
        localStorage.setItem(this.tokenKey, token);
        this.user.set(user);
    }

    private saveSession(response: AuthResponse): void {
        localStorage.setItem(this.tokenKey, response.token);
        this.user.set(response.user);
    }

    private csrfToken(): Observable<string> {
        return this.http.get<CsrfResponse>('/csrf-token').pipe(
            tap(() => undefined),
            switchMap((response) => new Observable<string>((subscriber) => {
                subscriber.next(response.token);
                subscriber.complete();
            })),
        );
    }

    private sessionHeaders(token: string): HttpHeaders {
        return new HttpHeaders({
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        });
    }
}
