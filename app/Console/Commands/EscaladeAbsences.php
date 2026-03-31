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

        $absencesN1 = Absence::where('statut', 'en_validation_n1')
            ->where('updated_at', '<=', $dateLimite)
            ->get();

        foreach ($absencesN1 as $absence) {
            $absence->update([
                'statut' => 'en_validation_n2',
                'niveau_validation_actuel' => 2,
            ]);

            $inspecteur = User::role('inspecteur')
                ->where('iepp_id', $absence->iepp_id)
                ->first();

            if ($inspecteur) {
                $inspecteur->notify(new AbsenceSoumise($absence));
            }

            Log::info("Absence {$absence->reference} escaladée N1 → N2 (48h sans réponse)");
        }

        $absencesN2 = Absence::where('statut', 'en_validation_n2')
            ->where('updated_at', '<=', $dateLimite)
            ->get();

        foreach ($absencesN2 as $absence) {
            $absence->update([
                'statut' => 'en_validation_n3',
                'niveau_validation_actuel' => 3,
            ]);

            $adminDrena = User::role('admin_drena')
                ->where('drena_id', $absence->drena_id)
                ->first();

            if ($adminDrena) {
                $adminDrena->notify(new AbsenceSoumise($absence));
            }

            Log::info("Absence {$absence->reference} escaladée N2 → N3 (48h sans réponse)");
        }

        $total = $absencesN1->count() + $absencesN2->count();
        $this->info("{$total} absence(s) escaladée(s).");

        return self::SUCCESS;
    }
}
