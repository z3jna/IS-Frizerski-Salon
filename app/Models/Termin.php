<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Termin extends Model
{
    use HasFactory;

    protected $table = 'termini';

    protected $fillable = [
        'datum',
        'vreme_pocetka',
        'vreme_zavrsetka',
        'status',
        'napomena',
        'klijent_id',
        'zaposleni_id',
        'usluga_id',
    ];

    protected function casts(): array
    {
        return [
            'datum' => 'date',
        ];
    }

    public function klijent(): BelongsTo
    {
        return $this->belongsTo(Klijent::class);
    }

    public function zaposleni(): BelongsTo
    {
        return $this->belongsTo(Zaposleni::class);
    }

    public function usluga(): BelongsTo
    {
        return $this->belongsTo(Usluga::class);
    }

    public function evidencijaTretmana(): HasOne
    {
        return $this->hasOne(EvidencijaTretmana::class);
    }

    public function racun(): HasOne
    {
        return $this->hasOne(Racun::class);
    }
}
