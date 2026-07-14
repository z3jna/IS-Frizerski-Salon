<?php

namespace App\Http\Controllers;

use App\Models\Racun;
use App\Models\Termin;
use App\Models\Usluga;
use App\Models\Zaposleni;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IzvestajController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $stats = [
            'zakazani' => Termin::where('status', 'zakazan')->count(),
            'realizovani' => Termin::where('status', 'realizovan')->count(),
            'otkazani' => Termin::where('status', 'otkazan')->count(),
            'prihodi' => Racun::where('status_placanja', 'placeno')->sum('ukupan_iznos'),
        ];

        $najtrazenijeUsluge = Usluga::query()
            ->select('usluge.*', DB::raw('COUNT(termini.id) as termini_count'))
            ->leftJoin('termini', 'termini.usluga_id', '=', 'usluge.id')
            ->groupBy('usluge.id', 'usluge.naziv', 'usluge.tip_usluge', 'usluge.opis', 'usluge.trajanje_minuta', 'usluge.cena', 'usluge.dostupnost', 'usluge.created_at', 'usluge.updated_at')
            ->orderByDesc('termini_count')
            ->limit(10)
            ->get();

        $ucinakZaposlenih = Zaposleni::withCount([
            'termini as zakazani_count' => fn ($query) => $query->where('status', 'zakazan'),
            'termini as realizovani_count' => fn ($query) => $query->where('status', 'realizovan'),
            'termini as otkazani_count' => fn ($query) => $query->where('status', 'otkazan'),
        ])->get();

        return view('izvestaji.index', compact('stats', 'najtrazenijeUsluge', 'ucinakZaposlenih'));
    }
}
