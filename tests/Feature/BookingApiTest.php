<?php

namespace Tests\Feature;

use App\Models\Klijent;
use App\Models\Termin;
use App\Models\User;
use App\Models\Usluga;
use App\Models\Zaposleni;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Klijent $klijent;

    private Zaposleni $zaposleni;

    private Usluga $usluga;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-07-14 07:00:00');

        $this->user = User::create([
            'name' => 'Ana Klijent',
            'email' => 'ana-test@salon.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_KLIJENT,
            'api_token' => 'booking-token',
        ]);
        $this->klijent = Klijent::create([
            'user_id' => $this->user->id,
            'ime' => 'Ana',
            'prezime' => 'Klijent',
        ]);
        $this->zaposleni = Zaposleni::create(['ime' => 'Mila', 'prezime' => 'Frizer']);
        $this->usluga = Usluga::create([
            'naziv' => 'Šišanje',
            'tip_usluge' => 'Kosa',
            'trajanje_minuta' => 60,
            'cena' => 2000,
            'dostupnost' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_options_return_only_available_services_and_employees(): void
    {
        Usluga::create([
            'naziv' => 'Nedostupna usluga',
            'tip_usluge' => 'Kosa',
            'trajanje_minuta' => 30,
            'cena' => 1000,
            'dostupnost' => false,
        ]);

        $this->withToken('booking-token')
            ->getJson('/api/zakazivanje/opcije')
            ->assertOk()
            ->assertJsonCount(1, 'data.usluge')
            ->assertJsonCount(1, 'data.zaposleni')
            ->assertJsonPath('data.usluge.0.id', $this->usluga->id);
    }

    public function test_available_slots_reject_past_date(): void
    {
        $this->withToken('booking-token')
            ->getJson('/api/zakazivanje/dostupni-termini?datum=2026-07-13&zaposleni_id='.$this->zaposleni->id.'&usluga_id='.$this->usluga->id)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('datum');
    }

    public function test_booking_rejects_incomplete_input(): void
    {
        $this->withToken('booking-token')
            ->postJson('/api/zakazivanje/termini', ['datum' => '2026-07-15'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vreme_pocetka', 'zaposleni_id', 'usluga_id']);
    }

    public function test_available_slots_stay_inside_working_hours(): void
    {
        $slots = $this->withToken('booking-token')
            ->getJson('/api/zakazivanje/dostupni-termini?datum=2026-07-15&zaposleni_id='.$this->zaposleni->id.'&usluga_id='.$this->usluga->id)
            ->assertOk()
            ->json('data');

        $this->assertSame('08:00', $slots[0]['vreme_pocetka']);
        $this->assertSame('20:00', $slots[array_key_last($slots)]['vreme_zavrsetka']);
    }

    public function test_client_can_book_and_client_id_is_derived_from_token(): void
    {
        $this->withToken('booking-token')
            ->postJson('/api/zakazivanje/termini', [
                'datum' => '2026-07-15',
                'vreme_pocetka' => '10:00',
                'zaposleni_id' => $this->zaposleni->id,
                'usluga_id' => $this->usluga->id,
                'klijent_id' => 999999,
            ])
            ->assertCreated()
            ->assertJsonPath('data.klijent_id', $this->klijent->id)
            ->assertJsonPath('data.status', 'zakazan');
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        Termin::create([
            'datum' => '2026-07-15',
            'vreme_pocetka' => '10:00',
            'vreme_zavrsetka' => '11:00',
            'status' => 'zakazan',
            'klijent_id' => $this->klijent->id,
            'zaposleni_id' => $this->zaposleni->id,
            'usluga_id' => $this->usluga->id,
        ]);

        $this->withToken('booking-token')
            ->postJson('/api/zakazivanje/termini', [
                'datum' => '2026-07-15',
                'vreme_pocetka' => '10:30',
                'zaposleni_id' => $this->zaposleni->id,
                'usluga_id' => $this->usluga->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('vreme_pocetka');
    }

    #[DataProvider('outsideWorkingHours')]
    public function test_booking_outside_working_hours_is_rejected(string $start): void
    {
        $this->withToken('booking-token')
            ->postJson('/api/zakazivanje/termini', [
                'datum' => '2026-07-15',
                'vreme_pocetka' => $start,
                'zaposleni_id' => $this->zaposleni->id,
                'usluga_id' => $this->usluga->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('vreme_pocetka');
    }

    public static function outsideWorkingHours(): array
    {
        return [
            'pre otvaranja' => ['07:30'],
            'završava posle zatvaranja' => ['19:30'],
        ];
    }
}
