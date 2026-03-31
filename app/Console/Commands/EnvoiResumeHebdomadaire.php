<?php

namespace App\Console\Commands;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;

class EnvoiResumeHebdomadaire extends Command
{
    protected $signature = 'absences:resume-hebdo';
    protected $description = 'Envoie un résumé hebdomadaire des absences aux chefs d\'établissement';

    public function handle(): int
    {
        $chefs = User::role('chef_etablissement')
            ->where('actif', true)
            ->whereNotNull('etablissement_id')
            ->get();

        $count = 0;

        foreach ($chefs as $chef) {
            $absencesSemaine = Absence::where('etablissement_id', $chef->etablissement_id)
                ->where('statut', 'approuvee')
                ->where('date_debut', '>=', now()->startOfWeek())
                ->where('date_debut', '<=', now()->endOfWeek())
                ->count();

            $enAttente = Absence::where('etablissement_id', $chef->etablissement_id)
                ->where('statut', 'en_validation_n1')
                ->count();

            $enCours = Absence::where('etablissement_id', $chef->etablissement_id)
                ->enCours()
                ->count();

            if ($absencesSemaine > 0 || $enAttente > 0 || $enCours > 0) {
                $chef->notify(new \App\Notifications\ResumeHebdomadaire(
                    $absencesSemaine,
                    $enAttente,
                    $enCours,
                    $chef->etablissement->nom ?? 'Votre établissement'
                ));
                $count++;
            }
        }

        $this->info("{$count} résumé(s) hebdomadaire(s) envoyé(s).");
        return self::SUCCESS;
    }
}
