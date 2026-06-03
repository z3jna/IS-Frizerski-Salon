<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klijent extends Model
{
    use HasFactory;

    protected $table = 'klijenti';

    protected $fillable = [
        'user_id',
        'ime',
        'prezime',
        'telefon',
        'adresa',
        'datum_rodjenja',
        'napomena',
        'preferencije',
    ];

    protected function casts(): array
    {
        return [
            'datum_rodjenja' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function termini(): HasMany
    {
        return $this->hasMany(Termin::class);
    }

    public function podsetnici(): HasMany
    {
        return $this->hasMany(Podsetnik::class);
    }
}
