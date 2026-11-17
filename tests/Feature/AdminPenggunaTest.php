<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPenggunaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_pengguna_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.pengguna.index'))
            ->assertOk()
            ->assertSee('Halaman pengguna')
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('admin.kendaraan.index'), false)
            ->assertSee(route('admin.transaksi.index'), false);
    }

    public function test_admin_can_search_users_by_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'name' => 'Ani Pratama',
            'email' => 'ani@example.test',
            'role' => 'petugas',
        ]);
        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'role' => 'owner',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pengguna.index', ['search' => 'ani@example.test']))
            ->assertOk()
            ->assertSee('Ani Pratama')
            ->assertDontSee('Budi Santoso')
            ->assertSee('value="ani@example.test"', false);
    }

    public function test_admin_can_open_the_user_creation_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.pengguna.create'))
            ->assertOk()
            ->assertSee(route('admin.pengguna.store'), false)
            ->assertSee('name="role"', false);
    }

    public function test_admin_can_create_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.pengguna.store'), [
            'name' => 'Petugas Baru',
            'email' => 'petugas@example.test',
            'password' => 'password123',
            'role' => 'petugas',
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'petugas@example.test',
            'role' => 'petugas',
        ]);
    }

    public function test_admin_can_edit_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pengguna = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($admin)
            ->get(route('admin.pengguna.edit', $pengguna))
            ->assertOk()
            ->assertSee($pengguna->name)
            ->assertSee('name="role"', false);
    }

    public function test_admin_can_update_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pengguna = User::factory()->create(['role' => 'petugas']);

        $response = $this->actingAs($admin)->put(route('admin.pengguna.update', $pengguna), [
            'name' => 'Nama Diperbarui',
            'email' => $pengguna->email,
            'role' => 'owner',
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseHas('users', [
            'id' => $pengguna->id,
            'name' => 'Nama Diperbarui',
            'role' => 'owner',
        ]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pengguna = User::factory()->create(['role' => 'petugas']);

        $response = $this->actingAs($admin)->delete(route('admin.pengguna.destroy', $pengguna));

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseMissing('users', ['id' => $pengguna->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete(route('admin.pengguna.destroy', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak bisa menghapus akun sendiri.');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_guest_is_redirected_to_login_from_the_pengguna_page(): void
    {
        $this->get('/admin/pengguna')->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_view_the_pengguna_page(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get('/admin/pengguna')
            ->assertForbidden();
    }
}
