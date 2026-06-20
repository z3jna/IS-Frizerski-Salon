import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiCollection, ApiItem, DostupanTermin, Termin } from '../models';

@Injectable({ providedIn: 'root' })
export class TerminiService {
    private readonly http = inject(HttpClient);

    all() {
        return this.http.get<ApiCollection<Termin>>('/api/termini');
    }

    create(payload: Record<string, unknown>) {
        return this.http.post<ApiItem<Termin>>('/api/termini', payload);
    }

    update(id: number, payload: Record<string, unknown>) {
        return this.http.put<ApiItem<Termin>>(`/api/termini/${id}`, payload);
    }

    cancel(id: number) {
        return this.http.delete<ApiItem<Termin> | { message: string }>(`/api/termini/${id}`);
    }

    forKlijent(id: number) {
        return this.http.get<ApiCollection<Termin>>(`/api/termini/klijent/${id}`);
    }

    forZaposleni(id: number) {
        return this.http.get<ApiCollection<Termin>>(`/api/termini/zaposleni/${id}`);
    }

    dostupniTermini(datum: string, zaposleniId: number, uslugaId: number) {
        const params = new HttpParams()
            .set('datum', datum)
            .set('zaposleni_id', zaposleniId)
            .set('usluga_id', uslugaId);

        return this.http.get<ApiCollection<DostupanTermin>>('/api/dostupni-termini', { params });
    }
}
