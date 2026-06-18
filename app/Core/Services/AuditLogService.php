<?php

namespace App\Core\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log an audit event.
     */
    public function log(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => array_merge($metadata, [
                'url' => Request::fullUrl(),
            ]),
        ]);
    }

    /**
     * Log a login event.
     */
    public function logLogin(?int $userId = null): AuditLog
    {
        return $this->log(
            action: 'login',
            description: 'User logged in',
            metadata: ['user_id_override' => $userId]
        );
    }

    /**
     * Log a logout event.
     */
    public function logLogout(): AuditLog
    {
        return $this->log(
            action: 'logout',
            description: 'User logged out'
        );
    }

    /**
     * Log a model creation.
     */
    public function logCreated(string $modelType, int $modelId, array $newValues = []): AuditLog
    {
        return $this->log(
            action: 'create',
            description: "Created {$modelType} #{$modelId}",
            modelType: $modelType,
            modelId: $modelId,
            newValues: $newValues
        );
    }

    /**
     * Log a model update.
     */
    public function logUpdated(string $modelType, int $modelId, array $oldValues = [], array $newValues = []): AuditLog
    {
        return $this->log(
            action: 'update',
            description: "Updated {$modelType} #{$modelId}",
            modelType: $modelType,
            modelId: $modelId,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Log a model deletion.
     */
    public function logDeleted(string $modelType, int $modelId, array $oldValues = []): AuditLog
    {
        return $this->log(
            action: 'delete',
            description: "Deleted {$modelType} #{$modelId}",
            modelType: $modelType,
            modelId: $modelId,
            oldValues: $oldValues
        );
    }

    /**
     * Log a reminder event.
     */
    public function logReminder(string $type, string $description, array $metadata = []): AuditLog
    {
        return $this->log(
            action: $type,
            description: $description,
            metadata: $metadata
        );
    }
}
