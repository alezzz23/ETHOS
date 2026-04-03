<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación enviada a la consultora y al líder cuando el desvío
 * de horas reales vs planificadas supera el umbral configurado (20%).
 */
class ProjectDeviationAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Project $project,
        private readonly float $deviationPercent,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->deviationPercent > 0
            ? "+{$this->deviationPercent}% sobre lo planificado"
            : "{$this->deviationPercent}% bajo lo planificado";

        return (new MailMessage)
            ->subject("⚠ Desvío en proyecto: {$this->project->title}")
            ->greeting("Hola {$notifiable->name}")
            ->line("El proyecto **{$this->project->title}** presenta un desvío del {$label}.")
            ->line("Horas planificadas: {$this->project->estimated_hours} h")
            ->line("Horas reales al corte: {$this->project->actual_hours} h")
            ->action('Ver proyecto', url("/admin/projects/{$this->project->id}"))
            ->line('Por favor revisa el avance y toma las medidas necesarias.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'               => 'project_deviation_alert',
            'project_id'         => $this->project->id,
            'project_title'      => $this->project->title,
            'deviation_percent'  => $this->deviationPercent,
            'estimated_hours'    => $this->project->estimated_hours,
            'actual_hours'       => $this->project->actual_hours,
        ];
    }
}
