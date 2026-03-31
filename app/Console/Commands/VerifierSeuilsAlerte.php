<?php

namespace App\Console\Commands;

use App\Models\Absence;
use App\Models\Drena;
use App\Models\Etablissement;
use App\Models\SeuilAlerte;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifierSeuilsAlerte extends Command
{
    protected $signature = 'absences:verifier-seuils';
    protected $description = 'Vérifie les seuils d\'alerte et notifie les responsables';

    public function handle(): int
    {
        $alertes = 0;

        // Vérifier les agents dépassant le seuil individuel (ex: 15 jours/an)
        $seuilAgent = 15;
        $agentsEnAlerte = User::actifs()
            ->whereHas('absences', function ($q) use ($seuilAgent) {
                $q->where('statut', 'approuvee')
                  ->whereYear('date_debut', now()->year)
                  ->havingRaw("SUM(nombre_jours) > ?", [$seuilAgent]);
            })
            ->get();

        foreach ($agentsEnAlerte as $agent) {
            $totalJours = $agent->absences()
                ->where('statut', 'approuvee')
                ->whereYear('date_debut', now()->year)
                ->sum('nombre_jours');

            // Notifier le chef d'établissement
            $chef = User::role('chef_etablissement')
                ->where('etablissement_id', $agent->etablissement_id)
                ->first();

            if ($chef) {
                $chef->notify(new \Illuminate\Notifications\DatabaseNotification([
                    'type' => 'seuil_alerte',
                    'message' => "{$agent->nom_complet} a atteint {$totalJours} jours d'absence cette année (seuil: {$seuilAgent}j).",
                ]));
                $alertes++;
            }

            Log::warning("Alerte seuil: {$agent->matricule} — {$totalJours}j d'absence (seuil: {$seuilAgent}j)");
        }

        // Vérifier les établissements avec taux anormal (> 10% des enseignants absents)
        $etablissements = Etablissement::actifs()
            ->withCount(['users as total_enseignants' => fn($q) => $q->actifs()])
            ->withCount(['absences as absences_en_cours' => fn($q) => $q->enCours()])
            ->get();

        foreach ($etablissements as $etab) {
            if ($etab->total_enseignants > 0) {
                $taux = ($etab->absences_en_cours / $etab->total_enseignants) * 100;
                if ($taux > 10) {
                    Log::warning("Alerte établissement: {$etab->nom} — taux {$taux}% ({$etab->absences_en_cours}/{$etab->total_enseignants})");
                    $alertes++;
                }
            }
        }

        $this->info("{$alertes} alerte(s) détectée(s).");
        return self::SUCCESS;
    }
}
