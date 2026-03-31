<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    protected $fillable = [
        'matricule', 'nom', 'prenoms', 'email', 'password', 'telephone',
        'telephone_secondary', 'genre', 'date_naissance', 'lieu_naissance',
        'photo', 'grade', 'echelon', 'specialite', 'date_integration',
        'date_prise_service', 'volume_horaire_hebdo', 'drena_id', 'iepp_id',
        'etablissement_id', 'statut', 'actif', 'two_factor_enabled',
        'two_factor_secret', 'delegue_par', 'delegation_debut', 'delegation_fin',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_naissance' => 'date',
            'date_integration' => 'date',
            'date_prise_service' => 'date',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'delegation_debut' => 'datetime',
            'delegation_fin' => 'datetime',
            'actif' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // ──── Relations ────

    public function drena(): BelongsTo
    {
        return $this->belongsTo(Drena::class);
    }

    public function iepp(): BelongsTo
    {
        return $this->belongsTo(Iepp::class);
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class, 'valideur_id');
    }

    public function congeSoldes(): HasMany
    {
        return $this->hasMany(CongeSolde::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function delegateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegue_par');
    }

    // ──── Accessors ────

    public function getNomCompletAttribute(): string
    {
        return "{$this->nom} {$this->prenoms}";
    }

    public function getInitialesAttribute(): string
    {
        return strtoupper(substr($this->nom, 0, 1) . substr($this->prenoms, 0, 1));
    }

    public function getAncienneteAttribute(): ?string
    {
        if (!$this->date_integration) return null;
        $diff = $this->date_integration->diff(now());
        return "{$diff->y} an(s) {$diff->m} mois";
    }

    // ──── Scopes ────

    public function scopeActifs($query)
    {
        return $query->where('actif', true)->where('statut', 'actif');
    }

    public function scopeParDrena($query, $drenaId)
    {
        return $query->where('drena_id', $drenaId);
    }

    public function scopeParEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }

    public function scopeEnseignants($query)
    {
        return $query->role('enseignant');
    }

    // ──── Methods ────

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedLogin(): void
    {
        $this->increment('failed_login_attempts');
        if ($this->failed_login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(30)]);
        }
    }

    public function resetFailedLogin(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    public function hasDelegation(): bool
    {
        return $this->delegation_debut &&
               $this->delegation_fin &&
               now()->between($this->delegation_debut, $this->delegation_fin);
    }

    public function getSoldeConge(?AnneeScolaire $annee = null): int
    {
        $annee = $annee ?? AnneeScolaire::enCours();
        if (!$annee) return 0;

        $solde = $this->congeSoldes()->where('annee_scolaire_id', $annee->id)->first();
        return $solde ? $solde->jours_restants : 30;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenoms', 'email', 'statut', 'etablissement_id', 'drena_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Utilisateur {$eventName}");
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    public function routeNotificationForSms(): string
    {
        return $this->telephone;
    }
}
