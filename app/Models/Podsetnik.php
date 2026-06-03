<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Podsetnik extends Model
{
    use HasFactory;

    protected $table = 'podsetnici';

    protected $fillable = [
        'klijent_id',
        'termin_id',
        'datum_slanja',
        'tip_podsetnika',
        'sadrzaj',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'datum_slanja' => 'datetime',
        ];
    }

    public function klijent(): BelongsTo
    {
        return $this->belongsTo(Klijent::class);
    }

    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }
}
