<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Racun extends Model
{
    use HasFactory;

    protected $table = 'racuni';

    protected $fillable = [
        'termin_id',
        'datum_izdavanja',
        'ukupan_iznos',
        'nacin_placanja',
        'status_placanja',
    ];

    protected function casts(): array
    {
        return [
            'datum_izdavanja' => 'date',
            'ukupan_iznos' => 'decimal:2',
        ];
    }

    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }

    public function uplate(): HasMany
    {
        return $this->hasMany(Uplata::class);
    }
}
