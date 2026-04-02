<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Project $project) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notif_email ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/admin/projects/{$this->project->id}");

        return (new MailMessage)
            ->subject("Nuevo proyecto requiere tu propuesta: {$this->project->title}")
            ->greeting("Hola {$notifiable->name},")
            ->line("El proyecto **{$this->project->title}** ha pasado a estado *En asignación* y requiere tu propuesta de servicio.")
            ->line("Cliente: {$this->project->client?->name}")
            ->line("Tienes **3 días hábiles** para subir la propuesta.")
            ->action('Ver proyecto', $url)
            ->line('Si tienes alguna duda, contacta al líder de proyectos.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'        => 'Nuevo proyecto para propuesta',
            'message'      => "El proyecto \"{$this->project->title}\" requiere tu propuesta en 3 días hábiles.",
            'level'        => 'warning',
            'action_url'   => "/admin/projects/{$this->project->id}",
            'action_label' => 'Ver proyecto',
        ];
    }
}
