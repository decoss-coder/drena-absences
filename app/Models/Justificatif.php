<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Justificatif extends Model
{
    protected $fillable = [
        'absence_id', 'nom_fichier', 'nom_original', 'chemin',
        'type_mime', 'taille', 'type_document', 'uploade_par',
    ];

    public function absence(): BelongsTo { return $this->belongsTo(Absence::class); }
    public function uploadeur(): BelongsTo { return $this->belongsTo(User::class, 'uploade_par'); }

    public function getTailleFormateeAttribute(): string
    {
        $bytes = $this->taille;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' Mo';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' Ko';
        return $bytes . ' octets';
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin);
    }
}
