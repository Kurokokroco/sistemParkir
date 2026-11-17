<?php

namespace Tests\Feature;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\TransaksiParkir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PetugasTransaksiTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_start_a_parking_transaction(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraan = $this->createKendaraan();
        $areaParkir = $this->createAreaParkir();

        $response = $this->actingAs($petugas)->post(route('petugas.transaksi.store'), [
            'kendaraan_id' => $kendaraan->id,
            'area_parkir_id' => $areaParkir->id,
        ]);

        $response->assertRedirect(route('petugas.transaksi.index'));
        $this->assertDatabaseHas('transaksi_parkir', [
            'kendaraan_id' => $kendaraan->id,
            'area_parkir_id' => $areaParkir->id,
            'waktu_keluar' => null,
        ]);
    }

    public function test_petugas_cannot_start_another_active_transaction_for_the_same_vehicle(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraan = $this->createKendaraan();
        $areaParkir = $this->createAreaParkir(kapasitas: 10);
        $this->createTransaksi($kendaraan, $areaParkir);

        $this->actingAs($petugas)
            ->from(route('petugas.transaksi.index'))
            ->post(route('petugas.transaksi.store'), [
                'kendaraan_id' => $kendaraan->id,
                'area_parkir_id' => $areaParkir->id,
            ])
            ->assertSessionHasErrors('kendaraan_id');

        $this->assertDatabaseCount('transaksi_parkir', 1);
    }

    public function test_petugas_can_checkout_a_vehicle_and_view_its_receipt(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraan = $this->createKendaraan();
        $areaParkir = $this->createAreaParkir();
        $waktuMasuk = Carbon::parse('2026-11-16 10:00:00');
        $this->travelTo($waktuMasuk);
        $transaksi = $this->createTransaksi($kendaraan, $areaParkir, now());
        $this->travelTo($waktuMasuk->copy()->addMinutes(30));

        $response = $this->actingAs($petugas)
            ->patch(route('petugas.transaksi.checkout', $transaksi));

        $response->assertRedirect(route('petugas.transaksi.index'));
        $this->assertDatabaseMissing('transaksi_parkir', [
            'id' => $transaksi->id,
            'waktu_keluar' => null,
        ]);
        $this->assertDatabaseHas('transaksi_parkir', [
            'id' => $transaksi->id,
            'biaya' => 2000,
        ]);

        $this->get(route('petugas.transaksi.receipt', $transaksi))
            ->assertOk()
            ->assertSee($kendaraan->plat_nomor)
            ->assertSee($areaParkir->nama_area)
            ->assertSee('Rp 2.000');
    }

    public function test_parking_fee_uses_vehicle_rate_and_rounds_up_partial_hours(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraan = $this->createKendaraan();
        $areaParkir = $this->createAreaParkir();
        $waktuMasuk = Carbon::parse('2026-11-16 10:00:00');
        $this->travelTo($waktuMasuk);
        $transaksi = $this->createTransaksi($kendaraan, $areaParkir, now());
        $this->travelTo($waktuMasuk->copy()->addSeconds(3601));

        $this->actingAs($petugas)
            ->patch(route('petugas.transaksi.checkout', $transaksi));

        $this->assertDatabaseHas('transaksi_parkir', [
            'id' => $transaksi->id,
            'biaya' => 4000,
        ]);
    }

    public function test_car_fee_is_five_thousand_per_started_hour(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB9876EF',
            'jenis_kendaraan' => 'mobil',
            'merk' => 'Toyota',
        ]);
        $areaParkir = $this->createAreaParkir('mobil');
        $waktuMasuk = Carbon::parse('2026-11-16 10:00:00');
        $this->travelTo($waktuMasuk);
        $transaksi = $this->createTransaksi($kendaraan, $areaParkir, now());
        $this->travelTo($waktuMasuk->copy()->addSeconds(3601));

        $this->actingAs($petugas)
            ->patch(route('petugas.transaksi.checkout', $transaksi));

        $this->assertDatabaseHas('transaksi_parkir', [
            'id' => $transaksi->id,
            'biaya' => 10000,
        ]);
    }

    public function test_petugas_cannot_exceed_area_capacity(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraanPemarkir = $this->createKendaraan();
        $kendaraanBaru = Kendaraan::create([
            'plat_nomor' => 'AB5678EF',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Yamaha',
        ]);
        $areaParkir = $this->createAreaParkir(kapasitas: 1);
        $this->createTransaksi($kendaraanPemarkir, $areaParkir);

        $this->actingAs($petugas)
            ->from(route('petugas.transaksi.index'))
            ->followingRedirects()
            ->post(route('petugas.transaksi.store'), [
                'kendaraan_id' => $kendaraanBaru->id,
                'area_parkir_id' => $areaParkir->id,
            ])
            ->assertOk()
            ->assertSee('Area parkir sudah penuh.');

        $this->assertDatabaseCount('transaksi_parkir', 1);
    }

    public function test_petugas_cannot_assign_vehicle_to_wrong_type_of_area(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kendaraanMobil = Kendaraan::create([
            'plat_nomor' => 'AB9876EF',
            'jenis_kendaraan' => 'mobil',
            'merk' => 'Toyota',
        ]);
        $areaMotor = $this->createAreaParkir('motor');

        $this->actingAs($petugas)
            ->from(route('petugas.transaksi.index'))
            ->post(route('petugas.transaksi.store'), [
                'kendaraan_id' => $kendaraanMobil->id,
                'area_parkir_id' => $areaMotor->id,
            ])
            ->assertSessionHasErrors('area_parkir_id');

        $this->assertDatabaseCount('transaksi_parkir', 0);
    }

    public function test_guest_is_redirected_to_login_from_transaction_list(): void
    {
        $this->get(route('petugas.transaksi.index'))->assertRedirect(route('login'));
    }

    public function test_admin_gets_a_separate_read_only_transaction_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.transaksi.index'))
            ->assertOk()
            ->assertSee('Monitoring Transaksi');
    }

    public function test_admin_can_search_transactions_by_vehicle_plate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = $this->createAreaParkir();
        $kendaraanCocok = $this->createKendaraan();
        $kendaraanLain = Kendaraan::create([
            'plat_nomor' => 'ZZ9876YY',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Yamaha',
        ]);
        $this->createTransaksi($kendaraanCocok, $areaParkir);
        $this->createTransaksi($kendaraanLain, $areaParkir);

        $this->actingAs($admin)
            ->get(route('admin.transaksi.index', ['search' => 'AB1234CD']))
            ->assertOk()
            ->assertSee('AB1234CD')
            ->assertDontSee('ZZ9876YY')
            ->assertSee('value="AB1234CD"', false);
    }

    public function test_petugas_can_search_transaction_history_by_vehicle_plate(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $areaParkir = $this->createAreaParkir();
        $kendaraanCocok = $this->createKendaraan();
        $kendaraanLain = Kendaraan::create([
            'plat_nomor' => 'ZZ9876YY',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Yamaha',
        ]);
        $this->createTransaksi($kendaraanCocok, $areaParkir);
        $this->createTransaksi($kendaraanLain, $areaParkir);

        $this->actingAs($petugas)
            ->get(route('petugas.transaksi.index', ['search' => 'AB1234CD']))
            ->assertOk()
            ->assertSee('AB1234CD')
            ->assertDontSee('ZZ9876YY')
            ->assertSee('value="AB1234CD"', false);
    }

    public function test_admin_cannot_access_the_petugas_transaction_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('petugas.transaksi.index'))
            ->assertForbidden();
    }

    public function test_petugas_can_still_access_transactions(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get(route('petugas.transaksi.index'))
            ->assertOk();
    }

    public function test_petugas_cannot_access_the_admin_transaction_page(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get(route('admin.transaksi.index'))
            ->assertForbidden();
    }

    private function createKendaraan(): Kendaraan
    {
        return Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);
    }

    private function createAreaParkir(string $jenisKendaraan = 'motor', int $kapasitas = 10): AreaParkir
    {
        return AreaParkir::factory()->create([
            'jenis_kendaraan' => $jenisKendaraan,
            'kapasitas' => $kapasitas,
        ]);
    }

    private function createTransaksi(
        Kendaraan $kendaraan,
        AreaParkir $areaParkir,
        ?Carbon $waktuMasuk = null,
    ): TransaksiParkir {
        return $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraan->id,
            'waktu_masuk' => $waktuMasuk ?? now()->subHour(),
        ]);
    }
}
