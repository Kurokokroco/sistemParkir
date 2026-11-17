<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKendaraanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_vehicle_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kendaraan.index'))
            ->assertOk()
            ->assertSee('AB1234CD')
            ->assertSee(route('admin.kendaraan.create'), false)
            ->assertSee(route('admin.transaksi.index'), false);
    }

    public function test_admin_can_search_vehicles_by_brand(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);
        Kendaraan::create([
            'plat_nomor' => 'CD5678EF',
            'jenis_kendaraan' => 'mobil',
            'merk' => 'Toyota',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kendaraan.index', ['search' => 'Honda']))
            ->assertOk()
            ->assertSee('AB1234CD')
            ->assertDontSee('CD5678EF')
            ->assertSee('value="Honda"', false);
    }

    public function test_admin_can_create_a_vehicle(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.kendaraan.store'), [
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);

        $response->assertRedirect(route('admin.kendaraan.index'));
        $this->assertDatabaseHas('kendaraan', [
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);
    }

    public function test_admin_can_open_the_vehicle_edit_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kendaraan.edit', $kendaraan))
            ->assertOk()
            ->assertSee('AB1234CD')
            ->assertSee(route('admin.kendaraan.update', $kendaraan), false);
    }

    public function test_admin_can_update_a_vehicle(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.kendaraan.update', $kendaraan), [
            'plat_nomor' => 'AB5678EF',
            'jenis_kendaraan' => 'mobil',
            'merk' => 'Toyota',
        ]);

        $response->assertRedirect(route('admin.kendaraan.index'));
        $this->assertDatabaseHas('kendaraan', [
            'id' => $kendaraan->id,
            'plat_nomor' => 'AB5678EF',
            'jenis_kendaraan' => 'mobil',
            'merk' => 'Toyota',
        ]);
    }

    public function test_admin_can_delete_a_vehicle(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kendaraan = Kendaraan::create([
            'plat_nomor' => 'AB1234CD',
            'jenis_kendaraan' => 'motor',
            'merk' => 'Honda',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.kendaraan.destroy', $kendaraan));

        $response->assertRedirect(route('admin.kendaraan.index'));
        $this->assertDatabaseMissing('kendaraan', ['id' => $kendaraan->id]);
    }

    public function test_guest_is_redirected_to_login_from_vehicle_list(): void
    {
        $this->get(route('admin.kendaraan.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_view_vehicle_list(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get(route('admin.kendaraan.index'))
            ->assertForbidden();
    }
}
