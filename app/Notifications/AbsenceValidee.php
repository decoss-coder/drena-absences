<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceValidee extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Absence $absence) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Absence approuvée - {$this->absence->reference}")
            ->greeting("Bonjour {$notifiable->prenoms},")
            ->line("Votre demande d'absence {$this->absence->reference} a été approuvée.")
            ->line("Période : du {$this->absence->date_debut->format('d/m/Y')} au {$this->absence->date_fin->format('d/m/Y')}")
            ->action('Voir le détail', url("/absences/{$this->absence->id}"));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'absence_validee',
            'absence_id' => $this->absence->id,
            'reference' => $this->absence->reference,
            'message' => "Votre absence {$this->absence->reference} a été approuvée.",
        ];
    }
}
