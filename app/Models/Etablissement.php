<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Etablissement extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'drena_id', 'iepp_id', 'code', 'nom', 'type', 'ordre_enseignement',
        'statut_juridique', 'localite', 'adresse', 'telephone', 'email',
        'latitude', 'longitude', 'effectif_eleves', 'effectif_enseignants', 'actif',
    ];

    protected $casts = ['actif' => 'boolean'];

    public function drena(): BelongsTo { return $this->belongsTo(Drena::class); }
    public function iepp(): BelongsTo { return $this->belongsTo(Iepp::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function absences(): HasMany { return $this->hasMany(Absence::class); }
    public function suppleances(): HasMany { return $this->hasMany(Suppleance::class); }

    public function enseignants(): HasMany
    {
        return $this->hasMany(User::class)->role('enseignant')->where('actif', true);
    }

    public function scopeActifs($q) { return $q->where('actif', true); }
    public function scopeParDrena($q, $id) { return $q->where('drena_id', $id); }
    public function scopeParIepp($q, $id) { return $q->where('iepp_id', $id); }

    public function getAbsencesDuJourAttribute()
    {
        return $this->absences()
            ->where('statut', 'approuvee')
            ->where('date_debut', '<=', today())
            ->where('date_fin', '>=', today())
            ->with('user')
            ->get();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['nom', 'actif', 'iepp_id', 'ordre_enseignement'])->logOnlyDirty();
    }

    public function estPrimaire(): bool
    {
        return $this->ordre_enseignement === 'primaire';
    }

    public function estSecondaire(): bool
    {
        return $this->ordre_enseignement === 'secondaire';
    }

    public function getCircuitValidationAttribute(): string
    {
        return $this->estPrimaire()
            ? 'Chef → Inspecteur → DRENA'
            : 'Chef → DRENA';
    }
}
