<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('DRENA Absences');
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::where('email', 'admin@education.gouv.ci')->first();

        $response = $this->post('/login', [
            'login' => 'admin@education.gouv.ci',
            'password' => 'Mena@2026',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_matricule(): void
    {
        $response = $this->post('/login', [
            'login' => 'MENA-001',
            'password' => 'Mena@2026',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@education.gouv.ci',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_account_locks_after_5_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'login' => 'admin@education.gouv.ci',
                'password' => 'wrong',
            ]);
        }

        $user = User::where('email', 'admin@education.gouv.ci')->first();
        $this->assertTrue($user->isLocked());
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@education.gouv.ci')->first();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::where('email', 'admin@education.gouv.ci')->first();
        $user->update(['actif' => false]);

        $response = $this->post('/login', [
            'login' => 'admin@education.gouv.ci',
            'password' => 'Mena@2026',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }
}
