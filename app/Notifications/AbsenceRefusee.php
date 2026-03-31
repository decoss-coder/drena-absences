<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceRefusee extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Absence $absence) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $lastValidation = $this->absence->validations()->latest()->first();
        return (new MailMessage)
            ->subject("Absence refusée - {$this->absence->reference}")
            ->greeting("Bonjour {$notifiable->prenoms},")
            ->line("Votre demande d'absence {$this->absence->reference} a été refusée.")
            ->when($lastValidation?->commentaire, fn($m) => $m->line("Motif : {$lastValidation->commentaire}"))
            ->action('Voir le détail', url("/absences/{$this->absence->id}"));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'absence_refusee',
            'absence_id' => $this->absence->id,
            'reference' => $this->absence->reference,
            'message' => "Votre absence {$this->absence->reference} a été refusée.",
        ];
    }
}
