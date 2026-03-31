<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    protected $fillable = [
        'absence_id', 'valideur_id', 'niveau', 'decision',
        'commentaire', 'date_validation', 'ip_address',
    ];

    protected $casts = ['date_validation' => 'datetime'];

    public function absence(): BelongsTo { return $this->belongsTo(Absence::class); }
    public function valideur(): BelongsTo { return $this->belongsTo(User::class, 'valideur_id'); }

    public function getDecisionBadgeAttribute(): array
    {
        return match($this->decision) {
            'approuvee' => ['label' => 'Approuvée', 'color' => 'emerald'],
            'refusee' => ['label' => 'Refusée', 'color' => 'red'],
            'complement_requis' => ['label' => 'Complément requis', 'color' => 'blue'],
            default => ['label' => $this->decision, 'color' => 'gray'],
        };
    }
}
