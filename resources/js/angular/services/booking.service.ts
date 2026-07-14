import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { forkJoin, map } from 'rxjs';
import { ApiCollection, ApiItem, BookingOptions, BookingPayload, DostupanTermin, Termin } from '../models';

@Injectable({ providedIn: 'root' })
export class BookingService {
    private readonly http = inject(HttpClient);

    options() {
        return forkJoin({
            usluge: this.http.get<ApiCollection<BookingOptions['usluge'][number]>>('/api/usluge'),
            zaposleni: this.http.get<ApiCollection<BookingOptions['zaposleni'][number]>>('/api/zaposleni'),
        }).pipe(map(({ usluge, zaposleni }) => ({
            data: {
                usluge: usluge.data,
                zaposleni: zaposleni.data,
            },
        })));
    }

    availableSlots(datum: string, zaposleniId: number, uslugaId: number) {
        const params = new HttpParams()
            .set('datum', datum)
            .set('zaposleni_id', zaposleniId)
            .set('usluga_id', uslugaId);

        return this.http.get<ApiCollection<DostupanTermin>>('/api/dostupni-termini', { params });
    }

    create(payload: BookingPayload) {
        return this.http.post<ApiItem<Termin>>('/api/termini', payload);
    }
}
