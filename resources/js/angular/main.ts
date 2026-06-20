import 'zone.js';
import '@angular/compiler';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { bootstrapApplication } from '@angular/platform-browser';
import { AppComponent } from './app.component';
import { authInterceptor } from './services/auth.interceptor';

if (document.querySelector('app-root')) {
    bootstrapApplication(AppComponent, {
        providers: [
            provideHttpClient(withInterceptors([authInterceptor])),
        ],
    }).catch((error: unknown) => {
        const message = error instanceof Error ? error.message : String(error);
        document.body.insertAdjacentHTML('beforeend', `<pre class="alert alert-danger">Angular bootstrap: ${message}</pre>`);
        console.error(error);
    });
}
