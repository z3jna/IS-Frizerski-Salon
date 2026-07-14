<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Račun</label>
        <select name="racun_id" class="form-select" required>
            @foreach($racuni as $racun)
                <option value="{{ $racun->id }}" @selected(old('racun_id', $uplata->racun_id ?? '') == $racun->id)>
                    #{{ $racun->id }} - {{ $racun->termin->klijent->ime }} {{ $racun->termin->klijent->prezime }} - {{ number_format($racun->ukupan_iznos, 2) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Datum uplate</label>
        <input type="date" name="datum_uplate" value="{{ old('datum_uplate', isset($uplata) ? $uplata->datum_uplate->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Iznos</label>
        <input type="number" step="0.01" name="iznos" value="{{ old('iznos', $uplata->iznos ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status transakcije</label>
        <select name="status_transakcije" class="form-select">
            @foreach(['uspesno' => 'Uspešno', 'na_cekanju' => 'Na čekanju', 'odbijeno' => 'Odbijeno'] as $status => $label)
                <option value="{{ $status }}" @selected(old('status_transakcije', $uplata->status_transakcije ?? 'uspesno') === $status)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
