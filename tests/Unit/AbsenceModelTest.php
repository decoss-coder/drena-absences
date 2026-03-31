<?php

namespace Tests\Unit;

use App\Models\Absence;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class AbsenceModelTest extends TestCase
{
    public function test_generer_reference_format(): void
    {
        $reference = 'ABS-' . date('Y') . '-00001';
        $this->assertStringStartsWith('ABS-' . date('Y'), $reference);
        $this->assertMatchesRegularExpression('/^ABS-\d{4}-\d{5}$/', $reference);
    }

    public function test_statut_badge_returns_correct_format(): void
    {
        $absence = new Absence();

        $absence->statut = 'approuvee';
        $badge = $absence->statut_badge;
        $this->assertEquals('Approuvée', $badge['label']);
        $this->assertEquals('emerald', $badge['color']);

        $absence->statut = 'refusee';
        $badge = $absence->statut_badge;
        $this->assertEquals('Refusée', $badge['label']);
        $this->assertEquals('red', $badge['color']);

        $absence->statut = 'en_validation_n1';
        $badge = $absence->statut_badge;
        $this->assertEquals('En attente', $badge['label']);
        $this->assertEquals('amber', $badge['color']);

        $absence->statut = 'annulee';
        $badge = $absence->statut_badge;
        $this->assertEquals('Annulée', $badge['label']);
        $this->assertEquals('gray', $badge['color']);
    }

    public function test_peut_etre_annulee(): void
    {
        $absence = new Absence();

        $absence->statut = 'brouillon';
        $this->assertTrue($absence->peutEtreAnnulee());

        $absence->statut = 'soumise';
        $this->assertTrue($absence->peutEtreAnnulee());

        $absence->statut = 'en_validation_n1';
        $this->assertTrue($absence->peutEtreAnnulee());

        $absence->statut = 'approuvee';
        $this->assertFalse($absence->peutEtreAnnulee());

        $absence->statut = 'refusee';
        $this->assertFalse($absence->peutEtreAnnulee());
    }
}
