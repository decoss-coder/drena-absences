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
        'statut', 'niveau_validation_actuel', 'declaree_par',
        'est_recurrente', 'pattern_recurrence',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'demi_journee_debut' => 'boolean',
        'demi_journee_fin' => 'boolean',
        'est_recurrente' => 'boolean',
    ];

    // ──── Relations ────

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function typeAbsence(): BelongsTo { return $this->belongsTo(TypeAbsence::class); }
    public function etablissement(): BelongsTo { return $this->belongsTo(Etablissement::class); }
    public function drena(): BelongsTo { return $this->belongsTo(Drena::class); }
    public function iepp(): BelongsTo { return $this->belongsTo(Iepp::class); }
    public function declarant(): BelongsTo { return $this->belongsTo(User::class, 'declaree_par'); }
    public function validations(): HasMany { return $this->hasMany(Validation::class)->orderBy('niveau'); }
    public function justificatifs(): HasMany { return $this->hasMany(Justificatif::class); }
    public function suppleance(): HasOne { return $this->hasOne(Suppleance::class); }

    // ──── Scopes ────

    public function scopeEnCours($q)
    {
        return $q->where('absences.date_debut', '<=', today())
                  ->where('absences.date_fin', '>=', today())
                  ->where('absences.statut', 'approuvee');
    }

    public function scopeEnAttente($q)
    {
        return $q->whereIn('absences.statut', ['soumise', 'en_validation_n1', 'en_validation_n2', 'en_validation_n3']);
    }

    public function scopeParStatut($q, string $statut) { return $q->where('absences.statut', $statut); }
    public function scopeParDrena($q, $id) { return $q->where('absences.drena_id', $id); }
    public function scopeParEtablissement($q, $id) { return $q->where('absences.etablissement_id', $id); }

    public function scopeParPeriode($q, $debut, $fin)
    {
        return $q->where('absences.date_debut', '>=', $debut)->where('absences.date_fin', '<=', $fin);
    }

    public function scopeAValiderPar($q, User $user)
    {
        if ($user->hasRole('chef_etablissement')) {
            return $q->where('absences.etablissement_id', $user->etablissement_id)
                      ->where('absences.statut', 'en_validation_n1');
        }
        if ($user->hasRole('inspecteur')) {
            return $q->where('absences.iepp_id', $user->iepp_id)
                      ->where('absences.statut', 'en_validation_n2');
        }
        if ($user->hasRole('admin_drena')) {
            return $q->where('absences.drena_id', $user->drena_id)
                      ->where('absences.statut', 'en_validation_n3');
        }
        return $q;
    }

    // ──── Accessors ────

    public function getStatutBadgeAttribute(): array
    {
        return match($this->statut) {
            'brouillon' => ['label' => 'Brouillon', 'color' => 'gray'],
            'soumise', 'en_validation_n1', 'en_validation_n2', 'en_validation_n3'
                => ['label' => 'En attente', 'color' => 'amber'],
            'approuvee' => ['label' => 'Approuvée', 'color' => 'emerald'],
            'refusee' => ['label' => 'Refusée', 'color' => 'red'],
            'annulee' => ['label' => 'Annulée', 'color' => 'gray'],
            'completement_requis' => ['label' => 'Complément requis', 'color' => 'blue'],
            default => ['label' => $this->statut, 'color' => 'gray'],
        };
    }

    public function getHeuresCoursPerduAttribute(): float
    {
        $volume = $this->user?->volume_horaire_hebdo ?? 0;
        if ($volume === 0) return 0;
        $heuresJour = $volume / 5;
        return round($heuresJour * $this->nombre_jours, 1);
    }

    // ──── Methods ────

    public static function genererReference(): string
    {
        $prefix = 'ABS';
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }

    public function soumettre(): void
    {
        $this->update([
            'statut' => 'en_validation_n1',
            'niveau_validation_actuel' => 1,
        ]);
    }

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
            'refusee' => $this->update(['statut' => 'refusee']),
            'complement_requis' => $this->update(['statut' => 'completement_requis']),
        };

        return $validation;
    }

    private function traiterApprobation(): void
    {
        $niveauMax = $this->typeAbsence->niveau_validation_requis;

        if ($this->nombre_jours > 10) $niveauMax = max($niveauMax, 3);
        elseif ($this->nombre_jours > 3) $niveauMax = max($niveauMax, 2);

        if ($this->niveau_validation_actuel >= $niveauMax) {
            $this->update(['statut' => 'approuvee']);
            $this->mettreAJourSoldeConge();
        } else {
            $prochainNiveau = $this->niveau_validation_actuel + 1;
            $this->update([
                'statut' => "en_validation_n{$prochainNiveau}",
                'niveau_validation_actuel' => $prochainNiveau,
            ]);
        }
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
        return in_array($this->statut, ['brouillon', 'soumise', 'en_validation_n1']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['statut', 'date_debut', 'date_fin', 'nombre_jours'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $e) => "Absence #{$this->reference} {$e}");
    }
}
