<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Naziv</label>
        <input name="naziv" value="{{ old('naziv', $usluga->naziv ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Tip usluge</label>
        <input name="tip_usluge" value="{{ old('tip_usluge', $usluga->tip_usluge ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Trajanje u minutima</label>
        <input type="number" name="trajanje_minuta" value="{{ old('trajanje_minuta', $usluga->trajanje_minuta ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Cena</label>
        <input type="number" step="0.01" name="cena" value="{{ old('cena', $usluga->cena ?? '') }}" class="form-control" required>
    </div>
    <div class="col-12">
        <label class="form-label">Opis</label>
        <textarea name="opis" class="form-control" rows="3">{{ old('opis', $usluga->opis ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="dostupnost" value="0">
            <input type="checkbox" name="dostupnost" value="1" class="form-check-input" id="dostupnost" @checked(old('dostupnost', $usluga->dostupnost ?? true))>
            <label class="form-check-label" for="dostupnost">Dostupna za zakazivanje</label>
        </div>
    </div>
</div>
