<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_receives_transaction_search_value(): void
    {
        $pemilik = User::factory()->create(['role' => 'owner']);

        $this->actingAs($pemilik)
            ->get(route('owner.dashboard', ['search' => 'AB1234CD']))
            ->assertOk()
            ->assertViewIs('owner.dashboard')
            ->assertViewHas('search', 'AB1234CD')
            ->assertSee('value="AB1234CD"', false);
    }
}
