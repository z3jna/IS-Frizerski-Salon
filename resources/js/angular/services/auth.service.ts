import { HttpClient } from '@angular/common/http';
import { Injectable, inject, signal } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { AuthResponse, User } from '../models';

@Injectable({ providedIn: 'root' })
export class AuthService {
    private readonly http = inject(HttpClient);
    private readonly tokenKey = 'salon_api_token';
    readonly user = signal<User | null>(null);

    token(): string | null {
        return localStorage.getItem(this.tokenKey);
    }

    login(payload: { email: string; password: string }): Observable<AuthResponse> {
        return this.http.post<AuthResponse>('/api/login', payload).pipe(tap((response) => this.saveAuth(response)));
    }

    register(payload: Record<string, unknown>): Observable<AuthResponse> {
        return this.http.post<AuthResponse>('/api/register', payload).pipe(tap((response) => this.saveAuth(response)));
    }

    loadUser(): Observable<{ user: User }> {
        return this.http.get<{ user: User }>('/api/user').pipe(tap((response) => this.user.set(response.user)));
    }

    logout(): Observable<{ message: string }> {
        return this.http.post<{ message: string }>('/api/logout', {}).pipe(tap(() => this.clearAuth()));
    }

    clearAuth(): void {
        localStorage.removeItem(this.tokenKey);
        this.user.set(null);
    }

    private saveAuth(response: AuthResponse): void {
        localStorage.setItem(this.tokenKey, response.token);
        this.user.set(response.user);
    }
}
