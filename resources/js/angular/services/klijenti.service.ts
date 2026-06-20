import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiCollection, Klijent } from '../models';

@Injectable({ providedIn: 'root' })
export class KlijentiService {
    private readonly http = inject(HttpClient);

    all() {
        return this.http.get<ApiCollection<Klijent>>('/api/klijenti');
    }
}
