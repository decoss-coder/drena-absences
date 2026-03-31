<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suppleance extends Model
{
    protected $fillable = [
        'absence_id', 'titulaire_id', 'suppleant_id', 'etablissement_id',
        'assigne_par', 'date_debut', 'date_fin', 'heures_effectuees',
        'statut', 'commentaire',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heures_effectuees' => 'decimal:1',
    ];

    public function absence(): BelongsTo { return $this->belongsTo(Absence::class); }
    public function titulaire(): BelongsTo { return $this->belongsTo(User::class, 'titulaire_id'); }
    public function suppleant(): BelongsTo { return $this->belongsTo(User::class, 'suppleant_id'); }
    public function etablissement(): BelongsTo { return $this->belongsTo(Etablissement::class); }
    public function assignateur(): BelongsTo { return $this->belongsTo(User::class, 'assigne_par'); }

    public function scopeEnCours($q)
    {
        return $q->where('statut', 'en_cours')
                  ->where('date_debut', '<=', today())
                  ->where('date_fin', '>=', today());
    }

    public static function trouverSuppleants(Absence $absence): \Illuminate\Support\Collection
    {
        return User::where('etablissement_id', $absence->etablissement_id)
            ->where('id', '!=', $absence->user_id)
            ->where('actif', true)
            ->where('statut', 'actif')
            ->where('specialite', $absence->user->specialite)
            ->whereDoesntHave('absences', function ($q) use ($absence) {
                $q->enCours()
                  ->where('date_debut', '<=', $absence->date_fin)
                  ->where('date_fin', '>=', $absence->date_debut);
            })
            ->get();
    }
}
