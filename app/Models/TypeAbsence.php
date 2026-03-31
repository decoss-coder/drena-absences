<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeAbsence extends Model
{
    protected $fillable = [
        'code', 'libelle', 'description', 'justificatif_obligatoire',
        'duree_max_jours', 'niveau_validation_requis', 'couleur',
        'icone', 'deductible_conge', 'actif', 'ordre',
    ];

    protected $casts = [
        'justificatif_obligatoire' => 'boolean',
        'deductible_conge' => 'boolean',
        'actif' => 'boolean',
    ];

    public function absences(): HasMany { return $this->hasMany(Absence::class); }
    public function scopeActifs($q) { return $q->where('actif', true)->orderBy('ordre'); }
}
