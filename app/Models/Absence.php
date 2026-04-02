<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Absence extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference', 'user_id', 'type_absence_id', 'etablissement_id',
        'drena_id', 'iepp_id', 'date_debut', 'date_fin', 'nombre_jours',
        'demi_journee_debut', 'demi_journee_fin', 'motif', 'commentaire_agent',
        'statut', 'niveau_validation_actuel', 'circuit_validation', 'declaree_par',
        'est_recurrente', 'pattern_recurrence',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'demi_journee_debut' => 'boolean',
        'demi_journee_fin' => 'boolean',
        'est_recurrente' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | CIRCUITS DE VALIDATION
    |--------------------------------------------------------------------------
    | Primaire  : chef d'établissement → inspecteur IEPP → DRENA
    | Secondaire: chef d'établissement → DRENA (pas d'inspecteur)
    */

    const CIRCUIT_PRIMAIRE   = 'primaire';
    const CIRCUIT_SECONDAIRE = 'secondaire';

    const STATUT_BROUILLON                = 'brouillon';
    const STATUT_EN_VALIDATION_CHEF       = 'en_validation_chef';
    const STATUT_EN_VALIDATION_INSPECTEUR = 'en_validation_inspecteur';
    const STATUT_EN_VALIDATION_DRENA      = 'en_validation_drena';
    const STATUT_APPROUVEE                = 'approuvee';
    const STATUT_REFUSEE                  = 'refusee';
    const STATUT_ANNULEE                  = 'annulee';
    const STATUT_COMPLEMENT_REQUIS        = 'complement_requis';

    const STATUTS_EN_ATTENTE = [
        'soumise',
        self::STATUT_EN_VALIDATION_CHEF,
        self::STATUT_EN_VALIDATION_INSPECTEUR,
        self::STATUT_EN_VALIDATION_DRENA,
    ];

    // ──── Relations ────

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function typeAbsence(): BelongsTo { return $this->belongsTo(TypeAbsence::class); }
    public function etablissement(): BelongsTo { return $this->belongsTo(Etablissement::class); }
    public function drena(): BelongsTo { return $this->belongsTo(Drena::class); }
    public function iepp(): BelongsTo { return $this->belongsTo(Iepp::class); }
    public function declarant(): BelongsTo { return $this->belongsTo(User::class, 'declaree_par'); }
    public function validations(): HasMany { return $this->hasMany(Validation::class)->orderBy('created_at'); }
    public function justificatifs(): HasMany { return $this->hasMany(Justificatif::class); }
    public function suppleance(): HasOne { return $this->hasOne(Suppleance::class); }

    // ──── Scopes ────

    public function scopeEnCours($q)
    {
        return $q->where('absences.date_debut', '<=', today())
                  ->where('absences.date_fin', '>=', today())
                  ->where('absences.statut', self::STATUT_APPROUVEE);
    }

    public function scopeEnAttente($q)
    {
        return $q->whereIn('absences.statut', self::STATUTS_EN_ATTENTE);
    }

    public function scopeParStatut($q, string $statut) { return $q->where('absences.statut', $statut); }
    public function scopeParDrena($q, $id) { return $q->where('absences.drena_id', $id); }
    public function scopeParEtablissement($q, $id) { return $q->where('absences.etablissement_id', $id); }
    public function scopeParPeriode($q, $debut, $fin)
    {
        return $q->where('absences.date_debut', '>=', $debut)->where('absences.date_fin', '<=', $fin);
    }

    /**
     * Absences que cet utilisateur peut valider selon son rôle ET le circuit.
     */
    public function scopeAValiderPar($q, User $user)
    {
        if ($user->hasRole('chef_etablissement')) {
            return $q->where('absences.etablissement_id', $user->etablissement_id)
                      ->where('absences.statut', self::STATUT_EN_VALIDATION_CHEF);
        }
        if ($user->hasRole('inspecteur')) {
            // L'inspecteur ne valide QUE le primaire
            return $q->where('absences.iepp_id', $user->iepp_id)
                      ->where('absences.statut', self::STATUT_EN_VALIDATION_INSPECTEUR)
                      ->where('absences.circuit_validation', self::CIRCUIT_PRIMAIRE);
        }
        if ($user->hasRole('admin_drena')) {
            return $q->where('absences.drena_id', $user->drena_id)
                      ->where('absences.statut', self::STATUT_EN_VALIDATION_DRENA);
        }
        return $q;
    }

    // ──── Accessors ────

    public function getStatutBadgeAttribute(): array
    {
        return match($this->statut) {
            self::STATUT_BROUILLON => ['label' => 'Brouillon', 'color' => 'gray'],
            'soumise', self::STATUT_EN_VALIDATION_CHEF
                => ['label' => 'Chef', 'color' => 'amber'],
            self::STATUT_EN_VALIDATION_INSPECTEUR
                => ['label' => 'Inspecteur', 'color' => 'amber'],
            self::STATUT_EN_VALIDATION_DRENA
                => ['label' => 'DRENA', 'color' => 'blue'],
            self::STATUT_APPROUVEE  => ['label' => 'Approuvée', 'color' => 'emerald'],
            self::STATUT_REFUSEE    => ['label' => 'Refusée', 'color' => 'red'],
            self::STATUT_ANNULEE    => ['label' => 'Annulée', 'color' => 'gray'],
            self::STATUT_COMPLEMENT_REQUIS => ['label' => 'Complément', 'color' => 'blue'],
            default => ['label' => $this->statut, 'color' => 'gray'],
        };
    }

    public function getProchainValideurAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_VALIDATION_CHEF       => 'Chef d\'établissement',
            self::STATUT_EN_VALIDATION_INSPECTEUR => 'Inspecteur IEPP',
            self::STATUT_EN_VALIDATION_DRENA      => 'Admin DRENA',
            default => '—',
        };
    }

    public function getCircuitLabelAttribute(): string
    {
        return match($this->circuit_validation) {
            self::CIRCUIT_PRIMAIRE   => 'Primaire (Chef → Inspecteur → DRENA)',
            self::CIRCUIT_SECONDAIRE => 'Secondaire (Chef → DRENA)',
            default => 'Non défini',
        };
    }

    public function getPasseParInspecteurAttribute(): bool
    {
        return $this->circuit_validation === self::CIRCUIT_PRIMAIRE;
    }

    public function getHeuresCoursPerduAttribute(): float
    {
        $volume = $this->user?->volume_horaire_hebdo ?? 0;
        if ($volume === 0) return 0;
        return round(($volume / 5) * $this->nombre_jours, 1);
    }

    // ──── Methods ────

    public static function genererReference(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('ABS-%s-%05d', $year, $count);
    }

    /**
     * Soumet l'absence. Détermine automatiquement le circuit
     * selon l'ordre d'enseignement de l'établissement.
     */
    public function soumettre(): void
    {
        $ordre = $this->etablissement?->ordre_enseignement ?? self::CIRCUIT_PRIMAIRE;

        $this->update([
            'statut' => self::STATUT_EN_VALIDATION_CHEF,
            'niveau_validation_actuel' => 1,
            'circuit_validation' => $ordre,
        ]);
    }

    /**
     * Enregistre une décision de validation.
     */
    public function valider(User $valideur, string $decision, ?string $commentaire = null): Validation
    {
        $validation = $this->validations()->create([
            'valideur_id' => $valideur->id,
            'niveau' => $this->niveau_validation_actuel,
            'decision' => $decision,
            'commentaire' => $commentaire,
            'date_validation' => now(),
            'ip_address' => request()->ip(),
        ]);

        match($decision) {
            'approuvee' => $this->traiterApprobation(),
            'refusee' => $this->update(['statut' => self::STATUT_REFUSEE]),
            'complement_requis' => $this->update(['statut' => self::STATUT_COMPLEMENT_REQUIS]),
        };

        return $validation;
    }

    /**
     * CŒUR DU WORKFLOW
     * Primaire  : chef → inspecteur → drena → approuvée
     * Secondaire: chef → drena → approuvée
     */
    private function traiterApprobation(): void
    {
        $prochain = $this->getProchainStatutApresApprobation();

        if ($prochain === null) {
            $this->update(['statut' => self::STATUT_APPROUVEE]);
            $this->mettreAJourSoldeConge();
        } else {
            $this->update([
                'statut' => $prochain,
                'niveau_validation_actuel' => $this->niveau_validation_actuel + 1,
            ]);
        }
    }

    /**
     * Pipeline de validation selon le circuit.
     * Retourne null quand le circuit est terminé.
     */
    private function getProchainStatutApresApprobation(): ?string
    {
        $niveauMax = $this->typeAbsence->niveau_validation_requis ?? 1;
        if ($this->nombre_jours > 10) $niveauMax = max($niveauMax, 3);
        elseif ($this->nombre_jours > 3) $niveauMax = max($niveauMax, 2);

        if ($this->circuit_validation === self::CIRCUIT_SECONDAIRE) {
            // ═══ SECONDAIRE : chef → drena ═══
            $pipeline = [
                self::STATUT_EN_VALIDATION_CHEF  => self::STATUT_EN_VALIDATION_DRENA,
                self::STATUT_EN_VALIDATION_DRENA => null,
            ];
            // ≤ 1 niveau requis et on vient du chef → fin
            if ($niveauMax <= 1 && $this->statut === self::STATUT_EN_VALIDATION_CHEF) {
                return null;
            }
        } else {
            // ═══ PRIMAIRE : chef → inspecteur → drena ═══
            $pipeline = [
                self::STATUT_EN_VALIDATION_CHEF       => self::STATUT_EN_VALIDATION_INSPECTEUR,
                self::STATUT_EN_VALIDATION_INSPECTEUR => self::STATUT_EN_VALIDATION_DRENA,
                self::STATUT_EN_VALIDATION_DRENA      => null,
            ];
            if ($niveauMax <= 1 && $this->statut === self::STATUT_EN_VALIDATION_CHEF) {
                return null;
            }
            if ($niveauMax <= 2 && $this->statut === self::STATUT_EN_VALIDATION_INSPECTEUR) {
                return null;
            }
        }

        return $pipeline[$this->statut] ?? null;
    }

    private function mettreAJourSoldeConge(): void
    {
        if (!$this->typeAbsence->deductible_conge) return;
        $annee = AnneeScolaire::enCours();
        if (!$annee) return;

        $solde = CongeSolde::firstOrCreate(
            ['user_id' => $this->user_id, 'annee_scolaire_id' => $annee->id],
            ['jours_acquis' => 30, 'jours_consommes' => 0, 'jours_restants' => 30]
        );
        $solde->increment('jours_consommes', $this->nombre_jours);
        $solde->update(['jours_restants' => $solde->jours_acquis - $solde->jours_consommes]);
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [
            self::STATUT_BROUILLON,
            'soumise',
            self::STATUT_EN_VALIDATION_CHEF,
        ]);
    }

    public function peutEtreValideePar(User $user): bool
    {
        return match($this->statut) {
            self::STATUT_EN_VALIDATION_CHEF =>
                $user->hasRole('chef_etablissement') && $user->etablissement_id === $this->etablissement_id,
            self::STATUT_EN_VALIDATION_INSPECTEUR =>
                $this->circuit_validation === self::CIRCUIT_PRIMAIRE
                && $user->hasRole('inspecteur') && $user->iepp_id === $this->iepp_id,
            self::STATUT_EN_VALIDATION_DRENA =>
                $user->hasRole('admin_drena') && $user->drena_id === $this->drena_id,
            default => false,
        };
    }

    /**
     * Retourne les étapes du workflow pour affichage visuel.
     */
    public function getEtapesWorkflow(): array
    {
        $etapes = [
            ['role' => 'Chef d\'établissement', 'statut' => self::STATUT_EN_VALIDATION_CHEF],
        ];
        if ($this->circuit_validation === self::CIRCUIT_PRIMAIRE) {
            $etapes[] = ['role' => 'Inspecteur IEPP', 'statut' => self::STATUT_EN_VALIDATION_INSPECTEUR];
        }
        $etapes[] = ['role' => 'Admin DRENA', 'statut' => self::STATUT_EN_VALIDATION_DRENA];
        return $etapes;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['statut', 'date_debut', 'date_fin', 'nombre_jours', 'circuit_validation'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $e) => "Absence #{$this->reference} {$e}");
    }
}
