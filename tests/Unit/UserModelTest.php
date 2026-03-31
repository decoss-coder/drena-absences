<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function test_nom_complet_accessor(): void
    {
        $user = new User();
        $user->nom = 'KOUADIO';
        $user->prenoms = 'Affoué Marie';

        $this->assertEquals('KOUADIO Affoué Marie', $user->nom_complet);
    }

    public function test_initiales_accessor(): void
    {
        $user = new User();
        $user->nom = 'BAMBA';
        $user->prenoms = 'Sékou';

        $this->assertEquals('BS', $user->initiales);
    }

    public function test_is_locked_returns_true_when_locked(): void
    {
        $user = new User();
        $user->locked_until = now()->addMinutes(15);

        $this->assertTrue($user->isLocked());
    }

    public function test_is_locked_returns_false_when_not_locked(): void
    {
        $user = new User();
        $user->locked_until = null;

        $this->assertFalse($user->isLocked());
    }

    public function test_is_locked_returns_false_when_lock_expired(): void
    {
        $user = new User();
        $user->locked_until = now()->subMinutes(5);

        $this->assertFalse($user->isLocked());
    }

    public function test_has_delegation_returns_false_without_dates(): void
    {
        $user = new User();
        $user->delegation_debut = null;
        $user->delegation_fin = null;

        $this->assertFalse($user->hasDelegation());
    }
}
