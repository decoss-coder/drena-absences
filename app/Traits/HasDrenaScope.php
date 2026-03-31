<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasDrenaScope
{
    /**
     * Applique automatiquement le scope DRENA selon le rôle de l'utilisateur connecté.
     * Le super_admin voit tout, les autres ne voient que leur périmètre.
     */
    public function scopeAccessibleParUtilisateur(Builder $query): Builder
    {
        $user = Auth::user();

        if (!$user || $user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('admin_drena') || $user->hasRole('gestionnaire_rh')) {
            return $query->where($this->getTable() . '.drena_id', $user->drena_id);
        }

        if ($user->hasRole('inspecteur')) {
            return $query->where($this->getTable() . '.iepp_id', $user->iepp_id);
        }

        if ($user->hasRole('chef_etablissement')) {
            return $query->where($this->getTable() . '.etablissement_id', $user->etablissement_id);
        }

        // Enseignant — ne voit que ses propres données
        if ($this->getTable() === 'absences') {
            return $query->where('user_id', $user->id);
        }

        return $query->where($this->getTable() . '.id', $user->id);
    }

    /**
     * Vérifie si l'utilisateur connecté peut accéder à cet enregistrement.
     */
    public function estAccessiblePar($user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user) return false;
        if ($user->hasRole('super_admin')) return true;

        $drenaId = $this->drena_id ?? null;
        $ieppId = $this->iepp_id ?? null;
        $etablissementId = $this->etablissement_id ?? null;

        if ($user->hasRole('admin_drena')) return $user->drena_id === $drenaId;
        if ($user->hasRole('inspecteur')) return $user->iepp_id === $ieppId;
        if ($user->hasRole('chef_etablissement')) return $user->etablissement_id === $etablissementId;

        return false;
    }
}
