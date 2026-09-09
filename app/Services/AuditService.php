<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Append-only Audit-Log.
 *
 * Einträge werden nur erzeugt, nie verändert oder gelöscht (kein Update/Delete).
 */
class AuditService
{
    public function log(
        string $action,
        ?string $targetType = null,
        ?string $targetId = null,
        string $result = 'success',
        ?array $meta = null,
        ?string $actorType = null,
        ?string $actorId = null,
        ?string $actorName = null,
    ): AuditLog {
        $actorType ??= 'user';
        $actorName ??= Auth::user()?->name ?? Auth::user()?->email;
        $actorId ??= (string) Auth::id();

        return AuditLog::create([
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'result' => $result,
            'ip' => $this->resolveIp(),
            'meta' => $meta,
        ]);
    }

    private function resolveIp(): ?string
    {
        $request = request();

        return $request instanceof Request ? $request->ip() : null;
    }
}
