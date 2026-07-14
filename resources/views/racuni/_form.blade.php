<div class="row g-3">
    @empty($racun)
        <div class="col-12">
            <label class="form-label">Realizovan termin</label>
            <select name="termin_id" class="form-select" required>
                @foreach($termini as $termin)
                    <option value="{{ $termin->id }}" @selected(old('termin_id', $selectedTermin ?? '') == $termin->id)>
                        #{{ $termin->id }} - {{ $termin->datum->format('d.m.Y') }} - {{ $termin->klijent->ime }} {{ $termin->klijent->prezime }} - {{ $termin->usluga->naziv }} ({{ number_format($termin->usluga->cena, 2) }})
                    </option>
                @endforeach
            </select>
        </div>
    @endempty
    <div class="col-md-4">
        <label class="form-label">Datum izdavanja</label>
        <input type="date" name="datum_izdavanja" value="{{ old('datum_izdavanja', isset($racun) ? $racun->datum_izdavanja->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Ukupan iznos</label>
        <input type="number" step="0.01" name="ukupan_iznos" value="{{ old('ukupan_iznos', $racun->ukupan_iznos ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Način plaćanja</label>
        <input name="nacin_placanja" value="{{ old('nacin_placanja', $racun->nacin_placanja ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status plaćanja</label>
        <select name="status_placanja" class="form-select">
            @foreach(['neplaceno' => 'Neplaćeno', 'delimicno' => 'Delimično', 'placeno' => 'Plaćeno'] as $status => $label)
                <option value="{{ $status }}" @selected(old('status_placanja', $racun->status_placanja ?? 'neplaceno') === $status)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
