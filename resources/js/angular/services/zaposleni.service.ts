import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiCollection, Zaposleni } from '../models';

@Injectable({ providedIn: 'root' })
export class ZaposleniService {
    private readonly http = inject(HttpClient);

    all() {
        return this.http.get<ApiCollection<Zaposleni>>('/api/zaposleni');
    }
}
