import { HttpInterceptorFn } from '@angular/common/http';

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
    }));
};
