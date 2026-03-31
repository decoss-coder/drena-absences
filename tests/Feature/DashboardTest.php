<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_super_admin_sees_mena_dashboard(): void
    {
        $user = User::role('super_admin')->first();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard National');
    }

    public function test_admin_drena_sees_drena_dashboard(): void
    {
        $user = User::role('admin_drena')->first();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_inspecteur_sees_inspecteur_dashboard(): void
    {
        $user = User::role('inspecteur')->first();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_chef_sees_chef_dashboard(): void
    {
        $user = User::role('chef_etablissement')->first();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_enseignant_sees_agent_dashboard(): void
    {
        $user = User::role('enseignant')->first();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Mon tableau de bord');
    }

    // ═══════ RBAC Tests ═══════

    public function test_enseignant_cannot_access_admin(): void
    {
        $user = User::role('enseignant')->first();
        $response = $this->actingAs($user)->get(route('admin.drenas.index'));
        $response->assertStatus(403);
    }

    public function test_enseignant_cannot_access_rapports(): void
    {
        $user = User::role('enseignant')->first();
        $response = $this->actingAs($user)->get(route('rapports.index'));
        $response->assertStatus(403);
    }

    public function test_admin_drena_cannot_access_super_admin(): void
    {
        $user = User::role('admin_drena')->first();
        $response = $this->actingAs($user)->get(route('admin.drenas.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_admin(): void
    {
        $user = User::role('super_admin')->first();
        $response = $this->actingAs($user)->get(route('admin.drenas.index'));
        $response->assertStatus(200);
    }

    public function test_chef_can_access_personnel(): void
    {
        $user = User::role('chef_etablissement')->first();
        $response = $this->actingAs($user)->get(route('personnel.index'));
        $response->assertStatus(200);
    }

    public function test_enseignant_cannot_create_personnel(): void
    {
        $user = User::role('enseignant')->first();
        $response = $this->actingAs($user)->get(route('personnel.create'));
        $response->assertStatus(403);
    }
}
