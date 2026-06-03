<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Uplata extends Model
{
    use HasFactory;

    protected $table = 'uplate';

    protected $fillable = [
        'racun_id',
        'datum_uplate',
        'iznos',
        'status_transakcije',
    ];

    protected function casts(): array
    {
        return [
            'datum_uplate' => 'date',
            'iznos' => 'decimal:2',
        ];
    }

    public function racun(): BelongsTo
    {
        return $this->belongsTo(Racun::class);
    }
}
