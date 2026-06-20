import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { ApiItem, TretmanPayload } from '../models';

@Injectable({ providedIn: 'root' })
export class TretmaniService {
    private readonly http = inject(HttpClient);

    create(payload: TretmanPayload) {
        return this.http.post<ApiItem<unknown>>('/api/tretmani', payload);
    }
}
