<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación enviada al líder cuando el proyecto es aprobado.
 */
class ProjectApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Project $project) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Proyecto aprobado: {$this->project->title}")
            ->greeting("Hola {$notifiable->name}")
            ->line("El proyecto **{$this->project->title}** ha sido aprobado y queda bajo tu liderazgo.")
            ->line("Cliente: {$this->project->client?->name}")
            ->line("Horas estimadas: {$this->project->estimated_hours} h")
            ->action('Ver proyecto', url("/admin/projects/{$this->project->id}"))
            ->line('Coordina el equipo y empieza la planificación de tareas.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'project_approved',
            'project_id'      => $this->project->id,
            'project_title'   => $this->project->title,
            'client_name'     => $this->project->client?->name,
            'estimated_hours' => $this->project->estimated_hours,
        ];
    }
}
