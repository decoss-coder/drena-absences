<?php

namespace App\Console\Commands;

use App\Models\Absence;
use App\Models\User;
use App\Notifications\AbsenceSoumise;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EscaladeAbsences extends Command
{
    protected $signature = 'absences:escalade';
    protected $description = 'Escalade automatique des absences non traitées après 48h';

    public function handle(): int
    {
        $heuresMax = config('drena.escalation_hours', 48);
        $dateLimite = now()->subHours($heuresMax);
        $total = 0;

        // Chef → Inspecteur (primaire uniquement) ou Chef → DRENA (secondaire)
        $absencesChef = Absence::where('statut', Absence::STATUT_EN_VALIDATION_CHEF)
            ->where('updated_at', '<=', $dateLimite)
            ->get();

        foreach ($absencesChef as $absence) {
            if ($absence->circuit_validation === Absence::CIRCUIT_SECONDAIRE) {
                // Secondaire : escalade directe vers DRENA
                $absence->update([
                    'statut' => Absence::STATUT_EN_VALIDATION_DRENA,
                    'niveau_validation_actuel' => 2,
                ]);
                $admin = User::role('admin_drena')->where('drena_id', $absence->drena_id)->first();
                if ($admin) $admin->notify(new AbsenceSoumise($absence));
            } else {
                // Primaire : escalade vers inspecteur
                $absence->update([
                    'statut' => Absence::STATUT_EN_VALIDATION_INSPECTEUR,
                    'niveau_validation_actuel' => 2,
                ]);
                $inspecteur = User::role('inspecteur')->where('iepp_id', $absence->iepp_id)->first();
                if ($inspecteur) $inspecteur->notify(new AbsenceSoumise($absence));
            }

            Log::info("Absence {$absence->reference} escaladée Chef → " .
                ($absence->circuit_validation === 'secondaire' ? 'DRENA' : 'Inspecteur') .
                " (48h sans réponse)");
            $total++;
        }

        // Inspecteur → DRENA (primaire uniquement)
        $absencesInspecteur = Absence::where('statut', Absence::STATUT_EN_VALIDATION_INSPECTEUR)
            ->where('updated_at', '<=', $dateLimite)
            ->get();

        foreach ($absencesInspecteur as $absence) {
            $absence->update([
                'statut' => Absence::STATUT_EN_VALIDATION_DRENA,
                'niveau_validation_actuel' => 3,
            ]);

            $admin = User::role('admin_drena')->where('drena_id', $absence->drena_id)->first();
            if ($admin) $admin->notify(new AbsenceSoumise($absence));

            Log::info("Absence {$absence->reference} escaladée Inspecteur → DRENA (48h sans réponse)");
            $total++;
        }

        $this->info("{$total} absence(s) escaladée(s).");
        return self::SUCCESS;
    }
}
