<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResumeHebdomadaire extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $absencesSemaine,
        public int $enAttente,
        public int $enCours,
        public string $etablissement
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Résumé hebdomadaire — {$this->etablissement}")
            ->greeting("Bonjour {$notifiable->prenoms},")
            ->line("Voici le résumé des absences de cette semaine pour {$this->etablissement} :")
            ->line("- Nouvelles absences cette semaine : {$this->absencesSemaine}")
            ->line("- Demandes en attente de validation : {$this->enAttente}")
            ->line("- Personnel absent actuellement : {$this->enCours}")
            ->action('Voir le tableau de bord', url('/dashboard'))
            ->line('Bonne semaine !');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'resume_hebdo',
            'message' => "Résumé : {$this->absencesSemaine} absence(s) cette semaine, {$this->enAttente} en attente, {$this->enCours} en cours.",
            'etablissement' => $this->etablissement,
        ];
    }
}
