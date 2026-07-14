import { Routes } from '@angular/router';
import { LoginComponent } from './components/login.component';
import { RegisterComponent } from './components/register.component';
import { TerminCreateComponent } from './components/termin-create.component';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
    { path: '', pathMatch: 'full', redirectTo: 'login' },
    { path: 'login', component: LoginComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'termini/create', component: TerminCreateComponent, canActivate: [authGuard] },
    { path: '**', redirectTo: 'login' },
];
