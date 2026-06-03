<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvidencijaTretmana extends Model
{
    use HasFactory;

    protected $table = 'evidencija_tretmana';

    protected $fillable = [
        'termin_id',
        'datum',
        'opis_tretmana',
        'nijansa',
        'proizvodjac',
        'formula',
        'korisceni_preparati',
        'napomena',
    ];

    protected function casts(): array
    {
        return [
            'datum' => 'date',
        ];
    }

    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }

    public function fotografije(): HasMany
    {
        return $this->hasMany(FotografijaTretmana::class);
    }
}
