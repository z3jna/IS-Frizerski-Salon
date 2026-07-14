import { HttpInterceptorFn } from '@angular/common/http';
import { tap } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (request, next) => {
    const token = localStorage.getItem('salon_api_token');

    if (! token) {
        return next(request);
    }

    return next(request.clone({
        setHeaders: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
        },
    })).pipe(
        tap({
            error: (error) => {
                if (error.status === 401) {
                    localStorage.removeItem('salon_api_token');
                }
            },
        }),
    );
};
