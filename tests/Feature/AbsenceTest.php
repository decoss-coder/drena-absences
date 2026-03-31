<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\Etablissement;
use App\Models\TypeAbsence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AbsenceTest extends TestCase
{
    use RefreshDatabase;

    private User $enseignant;
    private User $chef;
    private User $inspecteur;
    private User $adminDrena;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->enseignant = User::role('enseignant')->first();
        $this->chef = User::role('chef_etablissement')
            ->where('etablissement_id', $this->enseignant->etablissement_id)
            ->first();
        $this->inspecteur = User::role('inspecteur')
            ->where('iepp_id', $this->enseignant->iepp_id)
            ->first();
        $this->adminDrena = User::role('admin_drena')
            ->where('drena_id', $this->enseignant->drena_id)
            ->first();
    }

    public function test_enseignant_can_view_absences_list(): void
    {
        $response = $this->actingAs($this->enseignant)->get(route('absences.index'));
        $response->assertStatus(200);
    }

    public function test_enseignant_can_create_absence(): void
    {
        Notification::fake();

        $typeAbsence = TypeAbsence::where('code', 'CONVEN')->first();

        $response = $this->actingAs($this->enseignant)->post(route('absences.store'), [
            'type_absence_id' => $typeAbsence->id,
            'date_debut' => now()->addDays(1)->toDateString(),
            'date_fin' => now()->addDays(2)->toDateString(),
            'motif' => 'Motif de test pour convenance personnelle — rendez-vous important',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('absences', [
            'user_id' => $this->enseignant->id,
            'type_absence_id' => $typeAbsence->id,
            'statut' => 'en_validation_n1',
        ]);
    }

    public function test_absence_reference_is_generated_automatically(): void
    {
        Notification::fake();
        $typeAbsence = TypeAbsence::first();

        $this->actingAs($this->enseignant)->post(route('absences.store'), [
            'type_absence_id' => $typeAbsence->id,
            'date_debut' => now()->addDays(1)->toDateString(),
            'date_fin' => now()->addDays(1)->toDateString(),
            'motif' => 'Test de génération de référence automatique pour absence',
        ]);

        $absence = Absence::where('user_id', $this->enseignant->id)->latest()->first();
        $this->assertStringStartsWith('ABS-' . date('Y'), $absence->reference);
    }

    public function test_overlapping_absence_is_rejected(): void
    {
        Notification::fake();
        $typeAbsence = TypeAbsence::first();
        $dateDebut = now()->addDays(5)->toDateString();
        $dateFin = now()->addDays(7)->toDateString();

        // Première absence
        $this->actingAs($this->enseignant)->post(route('absences.store'), [
            'type_absence_id' => $typeAbsence->id,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'motif' => 'Première absence de test — pas de chevauchement attendu',
        ]);

        // Deuxième absence sur la même période
        $response = $this->actingAs($this->enseignant)->post(route('absences.store'), [
            'type_absence_id' => $typeAbsence->id,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'motif' => 'Deuxième absence de test — chevauchement attendu',
        ]);

        $response->assertSessionHasErrors('date_debut');
    }

    public function test_chef_can_validate_n1(): void
    {
        Notification::fake();
        $absence = Absence::factory()->create([
            'user_id' => $this->enseignant->id,
            'etablissement_id' => $this->enseignant->etablissement_id,
            'drena_id' => $this->enseignant->drena_id,
            'iepp_id' => $this->enseignant->iepp_id,
            'statut' => 'en_validation_n1',
            'niveau_validation_actuel' => 1,
        ]);

        $response = $this->actingAs($this->chef)->post(route('absences.valider', $absence), [
            'decision' => 'approuvee',
            'commentaire' => 'Validé sans réserve.',
        ]);

        $response->assertRedirect();
        $absence->refresh();

        // Si <= 3 jours, devrait être approuvée directement
        if ($absence->nombre_jours <= 3) {
            $this->assertEquals('approuvee', $absence->statut);
        }
    }

    public function test_chef_can_refuse_absence(): void
    {
        Notification::fake();
        $absence = Absence::factory()->create([
            'user_id' => $this->enseignant->id,
            'etablissement_id' => $this->enseignant->etablissement_id,
            'drena_id' => $this->enseignant->drena_id,
            'iepp_id' => $this->enseignant->iepp_id,
            'statut' => 'en_validation_n1',
            'niveau_validation_actuel' => 1,
        ]);

        $this->actingAs($this->chef)->post(route('absences.valider', $absence), [
            'decision' => 'refusee',
            'commentaire' => 'Justificatif insuffisant.',
        ]);

        $absence->refresh();
        $this->assertEquals('refusee', $absence->statut);
    }

    public function test_enseignant_cannot_validate(): void
    {
        $absence = Absence::factory()->create([
            'etablissement_id' => $this->enseignant->etablissement_id,
            'drena_id' => $this->enseignant->drena_id,
            'iepp_id' => $this->enseignant->iepp_id,
            'statut' => 'en_validation_n1',
        ]);

        $response = $this->actingAs($this->enseignant)->post(route('absences.valider', $absence), [
            'decision' => 'approuvee',
        ]);

        $response->assertStatus(403);
    }

    public function test_enseignant_can_cancel_pending_absence(): void
    {
        Notification::fake();
        $absence = Absence::factory()->create([
            'user_id' => $this->enseignant->id,
            'etablissement_id' => $this->enseignant->etablissement_id,
            'drena_id' => $this->enseignant->drena_id,
            'iepp_id' => $this->enseignant->iepp_id,
            'statut' => 'en_validation_n1',
            'declaree_par' => $this->enseignant->id,
        ]);

        $response = $this->actingAs($this->enseignant)->post(route('absences.annuler', $absence));

        $response->assertRedirect();
        $absence->refresh();
        $this->assertEquals('annulee', $absence->statut);
    }

    public function test_enseignant_cannot_cancel_approved_absence(): void
    {
        $absence = Absence::factory()->create([
            'user_id' => $this->enseignant->id,
            'etablissement_id' => $this->enseignant->etablissement_id,
            'drena_id' => $this->enseignant->drena_id,
            'iepp_id' => $this->enseignant->iepp_id,
            'statut' => 'approuvee',
        ]);

        $response = $this->actingAs($this->enseignant)->post(route('absences.annuler', $absence));
        $response->assertSessionHasErrors();
    }

    public function test_enseignant_only_sees_own_absences(): void
    {
        $otherUser = User::role('enseignant')->where('id', '!=', $this->enseignant->id)->first();

        Absence::factory()->create([
            'user_id' => $otherUser->id,
            'etablissement_id' => $otherUser->etablissement_id,
            'drena_id' => $otherUser->drena_id,
            'iepp_id' => $otherUser->iepp_id,
        ]);

        $response = $this->actingAs($this->enseignant)->get(route('absences.index'));
        $response->assertDontSee($otherUser->nom_complet);
    }

    public function test_super_admin_sees_all_absences(): void
    {
        $superAdmin = User::role('super_admin')->first();

        $response = $this->actingAs($superAdmin)->get(route('absences.index'));
        $response->assertStatus(200);
    }

    public function test_justificatif_upload_works(): void
    {
        Notification::fake();
        Storage::fake('local');

        $typeAbsence = TypeAbsence::where('justificatif_obligatoire', true)->first();

        $response = $this->actingAs($this->enseignant)->post(route('absences.store'), [
            'type_absence_id' => $typeAbsence->id,
            'date_debut' => now()->addDays(3)->toDateString(),
            'date_fin' => now()->addDays(4)->toDateString(),
            'motif' => 'Maladie nécessitant un certificat médical joint en pièce',
            'justificatifs' => [
                UploadedFile::fake()->create('certificat.pdf', 500, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('justificatifs', [
            'nom_original' => 'certificat.pdf',
        ]);
    }
}
