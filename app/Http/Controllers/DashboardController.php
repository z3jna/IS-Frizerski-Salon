<?php

namespace App\Http\Controllers;

use App\Models\Racun;
use App\Models\Termin;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $termini = Termin::with(['klijent', 'zaposleni', 'usluga'])
            ->when($user->role === User::ROLE_KLIJENT, fn ($query) => $query->where('klijent_id', $user->klijent?->id))
            ->when($user->role === User::ROLE_ZAPOSLENI, fn ($query) => $query->where('zaposleni_id', $user->zaposleni?->id))
            ->orderBy('datum')
            ->orderBy('vreme_pocetka')
            ->limit(8)
            ->get();

        $stats = [
            'zakazani' => Termin::where('status', 'zakazan')->count(),
            'realizovani' => Termin::where('status', 'realizovan')->count(),
            'otkazani' => Termin::where('status', 'otkazan')->count(),
            'prihodi' => Racun::where('status_placanja', 'placeno')->sum('ukupan_iznos'),
        ];

        return view('dashboard', compact('user', 'termini', 'stats'));
    }
}
