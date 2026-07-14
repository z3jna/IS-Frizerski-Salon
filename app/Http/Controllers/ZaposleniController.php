<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zaposleni;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ZaposleniController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $zaposleni = Zaposleni::withCount(['termini as realizovani_termini_count' => fn ($query) => $query->where('status', 'realizovan')])
            ->latest()
            ->paginate(15);

        return view('zaposleni.index', compact('zaposleni'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $users = User::where('role', User::ROLE_ZAPOSLENI)->doesntHave('zaposleni')->orderBy('name')->get();

        return view('zaposleni.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $this->validated($request);
        $this->attachUserData($data, User::ROLE_ZAPOSLENI);

        Zaposleni::create($data);

        return redirect()->route('zaposleni.index')->with('status', 'Zaposleni je dodat.');
    }

    public function show(Zaposleni $zaposleni): View
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->zaposleni?->is($zaposleni), 403);

        $zaposleni->load(['termini.klijent', 'termini.usluga']);

        return view('zaposleni.show', compact('zaposleni'));
    }

    public function edit(Zaposleni $zaposleni): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $users = User::where('role', User::ROLE_ZAPOSLENI)
            ->where(fn ($query) => $query->whereDoesntHave('zaposleni')->orWhere('id', $zaposleni->user_id))
            ->orderBy('name')
            ->get();

        return view('zaposleni.edit', compact('zaposleni', 'users'));
    }

    public function update(Request $request, Zaposleni $zaposleni): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $this->validated($request, $zaposleni);
        $this->attachUserData($data, User::ROLE_ZAPOSLENI, $zaposleni);

        $zaposleni->update($data);

        return redirect()->route('zaposleni.show', $zaposleni)->with('status', 'Podaci zaposlenog su sačuvani.');
    }

    public function destroy(Zaposleni $zaposleni): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $zaposleni->delete();

        return redirect()->route('zaposleni.index')->with('status', 'Zaposleni je obrisan.');
    }

    private function validated(Request $request, ?Zaposleni $zaposleni = null): array
    {
        $uniqueUser = Rule::unique('zaposleni', 'user_id');
        $ignoreUserId = $zaposleni?->user_id;
        if ($zaposleni) {
            $uniqueUser->ignore($zaposleni);
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
            'pozicija' => ['nullable', 'string', 'max:255'],
            'radno_vreme' => ['nullable', 'string', 'max:255'],
            'datum_zaposlenja' => ['nullable', 'date'],
            'plata' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    private function attachUserData(array &$data, string $role, ?Zaposleni $zaposleni = null): void
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        unset($data['email'], $data['password'], $data['password_confirmation']);

        $userId = $data['user_id'] ?? null;
        $user = $zaposleni?->user ?? ($userId ? User::find($userId) : null);

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
}
