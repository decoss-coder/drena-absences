<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Drena extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'code', 'nom', 'region', 'chef_lieu', 'telephone',
        'email', 'adresse', 'latitude', 'longitude', 'actif',
    ];

    protected $casts = ['actif' => 'boolean'];

    public function iepps(): HasMany { return $this->hasMany(Iepp::class); }
    public function etablissements(): HasMany { return $this->hasMany(Etablissement::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function absences(): HasMany { return $this->hasMany(Absence::class); }
    public function seuilAlertes(): HasMany { return $this->hasMany(SeuilAlerte::class); }

    public function scopeActives($q) { return $q->where('actif', true); }

    public function getTauxAbsenteismeAttribute(): float
    {
        $total = $this->users()->actifs()->count();
        if ($total === 0) return 0;
        $absents = $this->absences()
            ->where('statut', 'approuvee')
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->count();
        return round(($absents / $total) * 100, 2);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['nom', 'actif'])->logOnlyDirty();
    }
}
