<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Iepp extends Model
{
    use SoftDeletes;

    protected $fillable = ['drena_id', 'code', 'nom', 'localite', 'telephone', 'email', 'actif'];
    protected $casts = ['actif' => 'boolean'];

    public function drena(): BelongsTo { return $this->belongsTo(Drena::class); }
    public function etablissements(): HasMany { return $this->hasMany(Etablissement::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function absences(): HasMany { return $this->hasMany(Absence::class); }
    public function scopeActives($q) { return $q->where('actif', true); }
}
