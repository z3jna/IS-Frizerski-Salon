import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiCollection, ApiItem, BookingOptions, BookingPayload, DostupanTermin, Termin } from '../models';

@Injectable({ providedIn: 'root' })
export class BookingService {
    private readonly http = inject(HttpClient);

    options() {
        return this.http.get<ApiItem<BookingOptions>>('/api/zakazivanje/opcije');
    }

    availableSlots(datum: string, zaposleniId: number, uslugaId: number) {
        const params = new HttpParams()
            .set('datum', datum)
            .set('zaposleni_id', zaposleniId)
            .set('usluga_id', uslugaId);

        return this.http.get<ApiCollection<DostupanTermin>>('/api/zakazivanje/dostupni-termini', { params });
    }

    create(payload: BookingPayload) {
        return this.http.post<ApiItem<Termin>>('/api/zakazivanje/termini', payload);
    }
}
