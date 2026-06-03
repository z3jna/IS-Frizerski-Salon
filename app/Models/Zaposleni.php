<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zaposleni extends Model
{
    use HasFactory;

    protected $table = 'zaposleni';

    protected $fillable = [
        'user_id',
        'ime',
        'prezime',
        'telefon',
        'pozicija',
        'radno_vreme',
        'datum_zaposlenja',
        'plata',
    ];

    protected function casts(): array
    {
        return [
            'datum_zaposlenja' => 'date',
            'plata' => 'decimal:2',
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
}
