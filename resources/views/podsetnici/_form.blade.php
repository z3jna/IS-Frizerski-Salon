<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Klijent</label>
        <select name="klijent_id" class="form-select" required>
            @foreach($klijenti as $klijent)
                <option value="{{ $klijent->id }}" @selected(old('klijent_id', $podsetnik->klijent_id ?? '') == $klijent->id)>{{ $klijent->ime }} {{ $klijent->prezime }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Povezan termin</label>
        <select name="termin_id" class="form-select">
            <option value="">Bez termina</option>
            @foreach($termini as $termin)
                <option value="{{ $termin->id }}" @selected(old('termin_id', $podsetnik->termin_id ?? '') == $termin->id)>
                    #{{ $termin->id }} - {{ $termin->datum->format('d.m.Y') }} - {{ $termin->klijent->ime }} {{ $termin->klijent->prezime }} - {{ $termin->usluga->naziv }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Datum slanja</label>
        <input type="datetime-local" name="datum_slanja" value="{{ old('datum_slanja', isset($podsetnik) ? $podsetnik->datum_slanja->format('Y-m-d\\TH:i') : now()->addWeek()->format('Y-m-d\\TH:i')) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tip</label>
        <select name="tip_podsetnika" class="form-select">
            @foreach(['SMS', 'email', 'aplikacija'] as $tip)
                <option value="{{ $tip }}" @selected(old('tip_podsetnika', $podsetnik->tip_podsetnika ?? 'email') === $tip)>{{ $tip }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['planiran', 'poslat', 'neuspesan'] as $status)
                <option value="{{ $status }}" @selected(old('status', $podsetnik->status ?? 'planiran') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Sadržaj</label>
        <textarea name="sadrzaj" class="form-control" rows="4" required>{{ old('sadrzaj', $podsetnik->sadrzaj ?? '') }}</textarea>
    </div>
</div>
