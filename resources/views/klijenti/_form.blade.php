<div class="row g-3">
    @if(auth()->user()->isAdmin())
        <div class="col-md-6">
            <label class="form-label">Korisnički nalog</label>
            <select name="user_id" class="form-select">
                <option value="">Bez povezanog naloga</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $klijent->user_id ?? '') == $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-6">
        <label class="form-label">Ime</label>
        <input name="ime" value="{{ old('ime', $klijent->ime ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Prezime</label>
        <input name="prezime" value="{{ old('prezime', $klijent->prezime ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $klijent->user?->email ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Telefon</label>
        <input name="telefon" value="{{ old('telefon', $klijent->telefon ?? '') }}" class="form-control">
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
        <label class="form-label">Adresa</label>
        <input name="adresa" value="{{ old('adresa', $klijent->adresa ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Datum rođenja</label>
        <input type="date" name="datum_rodjenja" value="{{ old('datum_rodjenja', isset($klijent) && $klijent->datum_rodjenja ? $klijent->datum_rodjenja->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Preferencije</label>
        <textarea name="preferencije" class="form-control" rows="3">{{ old('preferencije', $klijent->preferencije ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Napomena</label>
        <textarea name="napomena" class="form-control" rows="3">{{ old('napomena', $klijent->napomena ?? '') }}</textarea>
    </div>
</div>
