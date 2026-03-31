<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeuilAlerte extends Model
{
    protected $fillable = ['drena_id', 'type', 'valeur', 'unite', 'action', 'actif'];
    protected $casts = ['actif' => 'boolean'];
    public function drena(): BelongsTo { return $this->belongsTo(Drena::class); }
}
