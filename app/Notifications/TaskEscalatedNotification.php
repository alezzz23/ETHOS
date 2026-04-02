<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskEscalatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/admin/projects/{$this->task->project_id}");

        return (new MailMessage)
            ->subject("⚠️ Tarea sin actividad: {$this->task->title}")
            ->greeting("Hola {$notifiable->name},")
            ->line("La siguiente tarea lleva más de **48 horas** sin actividad y ha sido escalada.")
            ->line("**Tarea:** {$this->task->title}")
            ->line("**Proyecto:** {$this->task->project?->title}")
            ->line("**Asignado a:** {$this->task->assignedTo?->name ?? 'Sin asignar'}")
            ->action('Ver proyecto', $url)
            ->line('Por favor toma acción inmediata.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'        => 'Tarea escalada por inactividad',
            'message'      => "\"{$this->task->title}\" lleva +48h sin actividad.",
            'level'        => 'danger',
            'action_url'   => "/admin/projects/{$this->task->project_id}",
            'action_label' => 'Ver proyecto',
        ];
    }
}
