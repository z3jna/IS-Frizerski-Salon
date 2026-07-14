import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { catchError, map, of } from 'rxjs';
import { AuthService } from '../services/auth.service';

export const authGuard: CanActivateFn = () => {
    const auth = inject(AuthService);
    const router = inject(Router);

    if (! auth.token()) {
        return router.parseUrl('/login');
    }

    if (auth.user()?.role === 'klijent') {
        return true;
    }

    return auth.loadUser().pipe(
        map(({ user }) => {
            if (user.role === 'klijent') {
                return true;
            }

            auth.clearAuth();
            return router.parseUrl('/login');
        }),
        catchError(() => {
            auth.clearAuth();
            return of(router.parseUrl('/login'));
        }),
    );
};
