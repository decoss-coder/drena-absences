<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceSoumise extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Absence $absence) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouvelle absence à valider - {$this->absence->reference}")
            ->greeting("Bonjour {$notifiable->prenoms},")
            ->line("{$this->absence->user->nom_complet} a déclaré une absence.")
            ->line("Type : {$this->absence->typeAbsence->libelle}")
            ->line("Période : du {$this->absence->date_debut->format('d/m/Y')} au {$this->absence->date_fin->format('d/m/Y')} ({$this->absence->nombre_jours} jour(s))")
            ->action('Voir la demande', url("/absences/{$this->absence->id}"))
            ->line('Merci de traiter cette demande dans les meilleurs délais.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'absence_soumise',
            'absence_id' => $this->absence->id,
            'reference' => $this->absence->reference,
            'agent' => $this->absence->user->nom_complet,
            'message' => "{$this->absence->user->nom_complet} a soumis une absence ({$this->absence->nombre_jours}j)",
        ];
    }
}
