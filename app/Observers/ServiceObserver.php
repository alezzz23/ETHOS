<?php

namespace App\Observers;

use App\Models\Service;
use App\Models\ServiceAuditLog;
use Illuminate\Support\Facades\Auth;

class ServiceObserver
{
    public function created(Service $service): void
    {
        $this->log($service, 'created', [], $service->toArray());
    }

    public function updated(Service $service): void
    {
        $dirty   = $service->getDirty();
        $changes = [];

        foreach ($dirty as $field => $newValue) {
            $changes[$field] = [
                'from' => $service->getOriginal($field),
                'to'   => $newValue,
            ];
        }

        // Determine action semantics for status toggling
        $action = 'updated';
        if (isset($dirty['status'])) {
            $action = $dirty['status'] === 'active' ? 'activated' : 'deactivated';
        }

        $this->log($service, $action, $changes, $service->toArray());
    }

    // ─── Helpers ───────────────────────────────────────────────────

    private function log(Service $service, string $action, array $changes, array $snapshot): void
    {
        ServiceAuditLog::create([
            'service_id' => $service->id,
            'action'     => $action,
            'snapshot'   => $snapshot,
            'changes'    => $changes ?: null,
            'changed_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
