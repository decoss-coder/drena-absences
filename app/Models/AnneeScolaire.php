<?php
// ═══════════════════════════════════════════════
// app/Models/AnneeScolaire.php
// ═══════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeScolaire extends Model
{
    protected $fillable = [
        'libelle', 'date_debut', 'date_fin', 'trimestre1_debut', 'trimestre1_fin',
        'trimestre2_debut', 'trimestre2_fin', 'trimestre3_debut', 'trimestre3_fin', 'en_cours',
    ];

    protected $casts = [
        'date_debut' => 'date', 'date_fin' => 'date',
        'trimestre1_debut' => 'date', 'trimestre1_fin' => 'date',
        'trimestre2_debut' => 'date', 'trimestre2_fin' => 'date',
        'trimestre3_debut' => 'date', 'trimestre3_fin' => 'date',
        'en_cours' => 'boolean',
    ];

    public function joursFeries(): HasMany { return $this->hasMany(JourFerie::class); }
    public function congeSoldes(): HasMany { return $this->hasMany(CongeSolde::class); }

    public static function enCours(): ?self
    {
        return static::where('en_cours', true)->first();
    }

    public function getTrimestreActuelAttribute(): ?int
    {
        $now = now();
        if ($now->between($this->trimestre1_debut, $this->trimestre1_fin)) return 1;
        if ($now->between($this->trimestre2_debut, $this->trimestre2_fin)) return 2;
        if ($now->between($this->trimestre3_debut, $this->trimestre3_fin)) return 3;
        return null;
    }
}
