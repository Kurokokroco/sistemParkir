<?php

namespace Tests\Feature;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAreaParkirTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_area_parkir_and_its_occupancy(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create([
            'nama_area' => 'Area Motor A',
            'jenis_kendaraan' => 'motor',
            'kapasitas' => 10,
        ]);
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);
        $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraan->id,
            'waktu_masuk' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.area_parkir.index'))
            ->assertOk()
            ->assertSee('Area Motor A')
            ->assertSee('1 / 10')
            ->assertSee(route('admin.area_parkir.create'), false);
    }

    public function test_admin_can_create_an_area(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.area_parkir.store'), [
            'nama_area' => 'Area Motor A',
            'jenis_kendaraan' => 'motor',
            'kapasitas' => 10,
        ]);

        $response->assertRedirect(route('admin.area_parkir.index'));
        $this->assertDatabaseHas('area_parkir', [
            'nama_area' => 'Area Motor A',
            'jenis_kendaraan' => 'motor',
            'kapasitas' => 10,
        ]);
    }

    public function test_admin_can_update_an_area(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.area_parkir.update', $areaParkir), [
            'nama_area' => 'Area Diperbarui',
            'jenis_kendaraan' => 'mobil',
            'kapasitas' => 20,
        ]);

        $response->assertRedirect(route('admin.area_parkir.index'));
        $this->assertDatabaseHas('area_parkir', [
            'id' => $areaParkir->id,
            'nama_area' => 'Area Diperbarui',
            'jenis_kendaraan' => 'mobil',
            'kapasitas' => 20,
        ]);
    }

    public function test_admin_cannot_reduce_capacity_below_active_vehicles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create([
            'jenis_kendaraan' => 'motor',
            'kapasitas' => 2,
        ]);

        foreach (['AB1234CD', 'AB5678EF'] as $platNomor) {
            $kendaraan = Kendaraan::create([
                'plat_nomor' => $platNomor,
                'jenis_kendaraan' => 'motor',
                'merk' => 'Honda',
            ]);
            $areaParkir->transaksiParkir()->create([
                'kendaraan_id' => $kendaraan->id,
                'waktu_masuk' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->put(route('admin.area_parkir.update', $areaParkir), [
                'nama_area' => $areaParkir->nama_area,
                'jenis_kendaraan' => 'motor',
                'kapasitas' => 1,
            ])
            ->assertSessionHasErrors('kapasitas');

        $this->assertDatabaseHas('area_parkir', [
            'id' => $areaParkir->id,
            'kapasitas' => 2,
        ]);
    }

    public function test_admin_cannot_change_area_type_while_vehicles_are_parked(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create(['jenis_kendaraan' => 'motor']);
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);
        $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraan->id,
            'waktu_masuk' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.area_parkir.update', $areaParkir), [
                'nama_area' => $areaParkir->nama_area,
                'jenis_kendaraan' => 'mobil',
                'kapasitas' => $areaParkir->kapasitas,
            ])
            ->assertSessionHasErrors('jenis_kendaraan');

        $this->assertDatabaseHas('area_parkir', [
            'id' => $areaParkir->id,
            'jenis_kendaraan' => 'motor',
        ]);
    }

    public function test_admin_cannot_delete_area_with_transaction_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create();
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => $areaParkir->jenis_kendaraan,
            'merk' => 'Honda',
        ]);
        $areaParkir->transaksiParkir()->create([
            'kendaraan_id' => $kendaraan->id,
            'waktu_masuk' => now(),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.area_parkir.index'))
            ->delete(route('admin.area_parkir.destroy', $areaParkir))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('area_parkir', ['id' => $areaParkir->id]);
    }

    public function test_admin_can_delete_an_unused_area(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $areaParkir = AreaParkir::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.area_parkir.destroy', $areaParkir));

        $response->assertRedirect(route('admin.area_parkir.index'));
        $this->assertDatabaseMissing('area_parkir', ['id' => $areaParkir->id]);
    }

    public function test_guest_is_redirected_to_login_from_area_parkir(): void
    {
        $this->get(route('admin.area_parkir.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_manage_area_parkir(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get(route('admin.area_parkir.index'))
            ->assertForbidden();
    }
}
