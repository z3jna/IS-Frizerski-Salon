<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usluga extends Model
{
    use HasFactory;

    protected $table = 'usluge';

    protected $fillable = [
        'naziv',
        'tip_usluge',
        'opis',
        'trajanje_minuta',
        'cena',
        'dostupnost',
    ];

    protected function casts(): array
    {
        return [
            'trajanje_minuta' => 'integer',
            'cena' => 'decimal:2',
            'dostupnost' => 'boolean',
        ];
    }

    public function termini(): HasMany
    {
        return $this->hasMany(Termin::class);
    }
}
