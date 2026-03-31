<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongeSolde extends Model
{
    protected $fillable = ['user_id', 'annee_scolaire_id', 'jours_acquis', 'jours_consommes', 'jours_restants'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function anneeScolaire(): BelongsTo { return $this->belongsTo(AnneeScolaire::class); }
}
