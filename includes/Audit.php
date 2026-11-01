<?php
/**
 * Câmara Digital - Sistema de Registro de Auditoria Global
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Tenant.php';

class Audit {
    public static function log(string $action, string $entity, ?int $entityId = null, ?string $details = null): void {
        try {
            $db = Database::getConnection();
            $tenantId = Tenant::id();
            $userId = $_SESSION['user_id'] ?? null;
            $userName = 'Visitante';

            if ($userId) {
                $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                if ($user) {
                    $userName = $user['name'];
                }
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $stmt = $db->prepare("
                INSERT INTO audit_logs (tenant_id, user_id, user_name, action, entity, entity_id, details, ip_address, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$tenantId, $userId, $userName, $action, $entity, $entityId, $details, $ip]);
        } catch (Exception $e) {
            // Silencioso para não interromper fluxo principal em caso de falha secundária de log
            error_log("Audit error: " . $e->getMessage());
        }
    }
}
