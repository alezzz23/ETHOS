<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskEscalatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EscalateStaleTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $threshold = now()->subHours(48);

        $staleTasks = Task::where('status', 'pending')
            ->where('type', 'proposal_upload')
            ->where('created_at', '<=', $threshold)
            ->whereNull('escalated_at')
            ->with(['project.client', 'assignedTo'])
            ->get();

        if ($staleTasks->isEmpty()) {
            return;
        }

        // Find project leaders to escalate to
        $leaders = User::whereHas('roles', fn ($q) => $q->where('name', 'lider_proyecto'))->get();

        foreach ($staleTasks as $task) {
            $task->update([
                'status'       => 'escalated',
                'escalated_at' => now(),
            ]);

            foreach ($leaders as $leader) {
                $leader->notify(new TaskEscalatedNotification($task));
            }
        }
    }
}
