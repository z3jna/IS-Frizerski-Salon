<div class="row g-3">
    @if(!auth()->user()->isKlijent())
        <div class="col-md-6">
            <label class="form-label">Klijent</label>
            <select name="klijent_id" class="form-select" required>
                @foreach($klijenti as $klijent)
                    <option value="{{ $klijent->id }}" @selected(old('klijent_id', $termin->klijent_id ?? '') == $klijent->id)>{{ $klijent->ime }} {{ $klijent->prezime }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-6">
        <label class="form-label">Zaposleni</label>
        <select name="zaposleni_id" class="form-select" required>
            @foreach($zaposleni as $radnik)
                <option value="{{ $radnik->id }}" @selected(old('zaposleni_id', $termin->zaposleni_id ?? '') == $radnik->id)>{{ $radnik->ime }} {{ $radnik->prezime }} - {{ $radnik->pozicija }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Usluga</label>
        <select name="usluga_id" class="form-select" required>
            @foreach($usluge as $usluga)
                <option value="{{ $usluga->id }}" @selected(old('usluga_id', $termin->usluga_id ?? '') == $usluga->id)>{{ $usluga->naziv }} - {{ $usluga->trajanje_minuta }} min - {{ number_format($usluga->cena, 2) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Datum</label>
        <input type="date" name="datum" value="{{ old('datum', isset($termin) ? $termin->datum->format('Y-m-d') : '') }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Vreme početka</label>
        <input type="time" name="vreme_pocetka" value="{{ old('vreme_pocetka', isset($termin) ? substr($termin->vreme_pocetka, 0, 5) : '') }}" class="form-control" required>
    </div>
    @isset($termin)
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['zakazan' => 'Zakazan', 'realizovan' => 'Realizovan', 'otkazan' => 'Otkazan'] as $status => $label)
                    <option value="{{ $status }}" @selected(old('status', $termin->status) === $status)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endisset
    <div class="col-12">
        <label class="form-label">Napomena</label>
        <textarea name="napomena" class="form-control" rows="3">{{ old('napomena', $termin->napomena ?? '') }}</textarea>
    </div>
</div>
