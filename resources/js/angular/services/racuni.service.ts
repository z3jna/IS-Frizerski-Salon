import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiCollection, Racun } from '../models';

@Injectable({ providedIn: 'root' })
export class RacuniService {
    private readonly http = inject(HttpClient);

    all() {
        return this.http.get<ApiCollection<Racun>>('/api/racuni');
    }
}
