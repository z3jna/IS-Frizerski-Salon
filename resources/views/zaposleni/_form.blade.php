<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Korisnički nalog</label>
        <select name="user_id" class="form-select">
            <option value="">Bez povezanog naloga</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $zaposleni->user_id ?? '') == $user->id)>{{ $user->name }} - {{ $user->email }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Pozicija</label>
        <input name="pozicija" value="{{ old('pozicija', $zaposleni->pozicija ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Ime</label>
        <input name="ime" value="{{ old('ime', $zaposleni->ime ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Prezime</label>
        <input name="prezime" value="{{ old('prezime', $zaposleni->prezime ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $zaposleni->user?->email ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Telefon</label>
        <input name="telefon" value="{{ old('telefon', $zaposleni->telefon ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Lozinka</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Potvrda lozinke</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Radno vreme</label>
        <input name="radno_vreme" value="{{ old('radno_vreme', $zaposleni->radno_vreme ?? '') }}" class="form-control" placeholder="Pon-Pet 09-17">
    </div>
    <div class="col-md-6">
        <label class="form-label">Datum zaposlenja</label>
        <input type="date" name="datum_zaposlenja" value="{{ old('datum_zaposlenja', isset($zaposleni) && $zaposleni->datum_zaposlenja ? $zaposleni->datum_zaposlenja->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Plata</label>
        <input type="number" step="0.01" name="plata" value="{{ old('plata', $zaposleni->plata ?? '') }}" class="form-control">
    </div>
</div>
