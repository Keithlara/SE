<?php
// Audit Logger - Extended logging functionality

require_once('activity_logger.php');

class AuditLogger {
    // Log user authentication events
    public static function logAuth($action, $details = '', $username = '') {
        $username = $username ?: ($_SESSION['adminName'] ?? 'System');
        $details = 'Authentication: ' . $details;
        return logActivity($action, $details);
    }

    // Log CRUD operations
    public static function logCRUD($action, $entity, $entityId, $details = '') {
        $action = ucfirst($action) . ' ' . $entity;
        $details = $details ?: "$entity ID: $entityId";
        return logActivity($action, $details);
    }

    // Log system events
    public static function logSystem($action, $details = '') {
        return logActivity('System: ' . $action, $details);
    }

    // Log security-related events
    public static function logSecurity($action, $details = '') {
        return logActivity('Security: ' . $action, $details);
    }

    // Log data export events
    public static function logExport($entity, $filters = []) {
        $filterStr = !empty($filters) ? ' with filters: ' . json_encode($filters) : '';
        return logActivity('Export', "Exported $entity data$filterStr");
    }

    // Log batch operations
    public static function logBatch($action, $entity, $count, $details = '') {
        $details = $details ? "$count $entity records - $details" : "$count $entity records";
        return logActivity("Batch $action", $details);
    }
}

// Helper function to log before/after state for updates
function logUpdate($entity, $entityId, $oldData, $newData) {
    $changes = [];
    foreach ($newData as $key => $value) {
        if (isset($oldData[$key]) && $oldData[$key] != $value) {
            $changes[$key] = [
                'old' => $oldData[$key],
                'new' => $value
            ];
        }
    }
    
    if (!empty($changes)) {
        $details = json_encode([
            'entity_id' => $entityId,
            'changes' => $changes
        ]);
        return AuditLogger::logCRUD('update', $entity, $entityId, $details);
    }
    return false;
}

// Helper function to log deletions
function logDeletion($entity, $entityId, $data = null) {
    $details = $data ? json_encode($data) : "ID: $entityId";
    return AuditLogger::logCRUD('delete', $entity, $entityId, $details);
}

// Helper function to log creations
function logCreation($entity, $entityId, $data = null) {
    $details = $data ? json_encode($data) : "ID: $entityId";
    return AuditLogger::logCRUD('create', $entity, $entityId, $details);
}
