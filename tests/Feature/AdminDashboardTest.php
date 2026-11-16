<?php

namespace Tests\Feature;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_the_admin_dashboard_after_login(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.transaksi.index'), false);
    }

    public function test_admin_dashboard_shows_parking_summary_capacity_recent_transactions_and_quick_links(): void
    {
        $this->travelTo(Carbon::parse('2026-11-16 13:00:00'));
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create([
            'nama_area' => 'Area Motor A',
            'jenis_kendaraan' => 'motor',
            'kapasitas' => 5,
        ]);
        $kendaraanAktif = $this->createKendaraan('AB1234CD');
        $kendaraanSelesai = $this->createKendaraan('AB5678EF');
        $kendaraanKemarin = $this->createKendaraan('AB9012GH');

        $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraanAktif->id,
            'waktu_masuk' => now()->subHour(),
        ]);
        $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraanSelesai->id,
            'waktu_masuk' => now()->subHours(2),
            'waktu_keluar' => now()->subHour(),
            'biaya' => 2000,
        ]);
        $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraanKemarin->id,
            'waktu_masuk' => now()->subDay()->subHours(2),
            'waktu_keluar' => now()->subDay()->subHour(),
            'biaya' => 5000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('kendaraanAktifCount', 1)
            ->assertViewHas('transaksiHariIniCount', 2)
            ->assertViewHas('pendapatanHariIni', 2000)
            ->assertSee('AB1234CD')
            ->assertSee('Area Motor A')
            ->assertSee('1 / 5')
            ->assertSee('Rp 2.000')
            ->assertSee('Di area parkir')
            ->assertSee(route('admin.pengguna.index'), false)
            ->assertSee(route('admin.kendaraan.index'), false)
            ->assertSee(route('admin.area_parkir.index'), false)
            ->assertSee(route('admin.transaksi.index'), false);
    }

    public function test_admin_dashboard_marks_area_as_nearly_full_at_eighty_percent_capacity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create([
            'nama_area' => 'Area Motor Penuh',
            'jenis_kendaraan' => 'motor',
            'kapasitas' => 5,
        ]);

        foreach (['AA1010AA', 'AA2020AA', 'AA3030AA', 'AA4040AA'] as $platNomor) {
            $kendaraan = $this->createKendaraan($platNomor);
            $areaParkir->transaksiParkir()->create([
                'kendaraan_id' => $kendaraan->id,
                'waktu_masuk' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('4 / 5')
            ->assertSee('Hampir penuh');
    }

    public function test_petugas_cannot_access_the_admin_dashboard(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_login_page_renders_with_a_valid_login_route(): void
    {
        $this->get('/login')->assertOk();
    }

    private function createKendaraan(string $platNomor): Kendaraan
    {
        return Kendaraan::create([
            'plat_nomor' => $platNomor,
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);
    }
}
