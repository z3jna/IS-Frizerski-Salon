<div class="row g-3">
    @empty($tretman)
        <div class="col-12">
            <label class="form-label">Termin</label>
            <select name="termin_id" class="form-select" required>
                @foreach($termini as $termin)
                    <option value="{{ $termin->id }}" @selected(old('termin_id', $selectedTermin ?? '') == $termin->id)>
                        #{{ $termin->id }} - {{ $termin->datum->format('d.m.Y') }} - {{ $termin->klijent->ime }} {{ $termin->klijent->prezime }} - {{ $termin->usluga->naziv }}
                    </option>
                @endforeach
            </select>
        </div>
    @endempty
    <div class="col-md-4">
        <label class="form-label">Datum</label>
        <input type="date" name="datum" value="{{ old('datum', isset($tretman) ? $tretman->datum->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Nijansa</label>
        <input name="nijansa" value="{{ old('nijansa', $tretman->nijansa ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Proizvođač</label>
        <input name="proizvodjac" value="{{ old('proizvodjac', $tretman->proizvodjac ?? '') }}" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Opis tretmana</label>
        <textarea name="opis_tretmana" class="form-control" rows="3" required>{{ old('opis_tretmana', $tretman->opis_tretmana ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Formula</label>
        <textarea name="formula" class="form-control" rows="3">{{ old('formula', $tretman->formula ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Korišćeni preparati</label>
        <textarea name="korisceni_preparati" class="form-control" rows="3">{{ old('korisceni_preparati', $tretman->korisceni_preparati ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Napomena</label>
        <textarea name="napomena" class="form-control" rows="2">{{ old('napomena', $tretman->napomena ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Fotografije pre</label>
        <input type="file" name="fotografije_pre[]" class="form-control" multiple accept="image/*">
    </div>
    <div class="col-md-6">
        <label class="form-label">Fotografije posle</label>
        <input type="file" name="fotografije_posle[]" class="form-control" multiple accept="image/*">
    </div>
    @isset($tretman)
        <div class="col-12">
            <div class="existing-photos">
                <div class="section-title">
                    <h2>Postojece fotografije</h2>
                    <p>Klik na fotografiju otvara original.</p>
                </div>
                @include('tretmani._photo_gallery', ['photos' => $tretman->fotografije])
            </div>
        </div>
    @endisset
</div>
