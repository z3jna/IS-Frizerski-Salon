<?php

namespace App\Http\Controllers;

use App\Models\Klijent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KlijentController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || $user->isZaposleni(), 403);

        $klijenti = Klijent::with('user')->latest()->paginate(15);

        return view('klijenti.index', compact('klijenti'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $users = User::where('role', User::ROLE_KLIJENT)->doesntHave('klijent')->orderBy('name')->get();

        return view('klijenti.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $this->validated($request);
        DB::transaction(function () use (&$data) {
            $this->attachUserData($data, User::ROLE_KLIJENT);
            Klijent::create($data);
        });

        return redirect()->route('klijenti.index')->with('status', 'Klijent je dodat.');
    }

    public function show(Klijent $klijent): View
    {
        $this->authorizeAccess($klijent);

        $klijent->load([
            'termini.usluga',
            'termini.zaposleni',
            'termini.evidencijaTretmana.fotografije',
            'podsetnici',
        ]);

        return view('klijenti.show', compact('klijent'));
    }

    public function edit(Klijent $klijent): View
    {
        $this->authorizeAccess($klijent);

        $users = User::where('role', User::ROLE_KLIJENT)
            ->where(fn ($query) => $query->whereDoesntHave('klijent')->orWhere('id', $klijent->user_id))
            ->orderBy('name')
            ->get();

        return view('klijenti.edit', compact('klijent', 'users'));
    }

    public function update(Request $request, Klijent $klijent): RedirectResponse
    {
        $this->authorizeAccess($klijent);

        $data = $this->validated($request, $klijent);
        DB::transaction(function () use (&$data, $klijent) {
            $this->attachUserData($data, User::ROLE_KLIJENT, $klijent);
            $klijent->update($data);
        });

        return redirect()->route('klijenti.show', $klijent)->with('status', 'Podaci klijenta su sačuvani.');
    }

    public function destroy(Klijent $klijent): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $klijent->delete();

        return redirect()->route('klijenti.index')->with('status', 'Klijent je obrisan.');
    }

    private function validated(Request $request, ?Klijent $klijent = null): array
    {
        $uniqueUser = Rule::unique('klijenti', 'user_id');
        $ignoreUserId = $klijent?->user_id;
        if ($klijent) {
            $uniqueUser->ignore($klijent);
        }

        $data = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                $uniqueUser,
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreUserId),
            ],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'ime' => ['required', 'string', 'max:255'],
            'prezime' => ['required', 'string', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'adresa' => ['nullable', 'string', 'max:255'],
            'datum_rodjenja' => ['nullable', 'date'],
            'napomena' => ['nullable', 'string'],
            'preferencije' => ['nullable', 'string'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    private function attachUserData(array &$data, string $role, ?Klijent $klijent = null): void
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        unset($data['email'], $data['password'], $data['password_confirmation']);

        $userId = $data['user_id'] ?? null;
        $user = $klijent?->user ?? ($userId ? User::find($userId) : null);

        if (! $user && $email) {
            $user = User::create([
                'name' => $data['ime'].' '.$data['prezime'],
                'email' => $email,
                'password' => Hash::make($password ?: 'password'),
                'role' => $role,
            ]);
            $data['user_id'] = $user->id;
            return;
        }

        if ($user) {
            $user->fill([
                'name' => $data['ime'].' '.$data['prezime'],
                'email' => $email ?: $user->email,
                'role' => $role,
            ]);

            if ($password) {
                $user->password = Hash::make($password);
            }

            $user->save();
            $data['user_id'] = $user->id;
        }
    }

    private function authorizeAccess(Klijent $klijent): void
    {
        $user = auth()->user();

        abort_unless($user->isAdmin() || $user->isZaposleni() || $user->klijent?->is($klijent), 403);
    }
}
