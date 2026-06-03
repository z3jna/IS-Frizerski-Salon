<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotografijaTretmana extends Model
{
    use HasFactory;

    protected $table = 'fotografije_tretmana';

    public $timestamps = false;

    protected $fillable = [
        'evidencija_tretmana_id',
        'naziv',
        'putanja',
        'tip_fotografije',
        'datum_dodavanja',
        'opis',
    ];

    protected function casts(): array
    {
        return [
            'datum_dodavanja' => 'datetime',
        ];
    }

    public function evidencijaTretmana(): BelongsTo
    {
        return $this->belongsTo(EvidencijaTretmana::class);
    }
}
