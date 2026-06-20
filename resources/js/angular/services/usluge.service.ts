import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiCollection, ApiItem, Usluga } from '../models';

@Injectable({ providedIn: 'root' })
export class UslugeService {
    private readonly http = inject(HttpClient);

    all() {
        return this.http.get<ApiCollection<Usluga>>('/api/usluge');
    }

    create(payload: Partial<Usluga>) {
        return this.http.post<ApiItem<Usluga>>('/api/usluge', payload);
    }
}
