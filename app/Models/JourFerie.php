<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourFerie extends Model
{
    protected $fillable = ['annee_scolaire_id', 'date', 'libelle', 'type'];
    protected $casts = ['date' => 'date'];
    public function anneeScolaire(): BelongsTo { return $this->belongsTo(AnneeScolaire::class); }
}
