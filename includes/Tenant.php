<?php
/**
 * Câmara Digital - Gerenciamento do Contexto Multi-Tenant
 */

require_once __DIR__ . '/../config/database.php';

class Tenant {
    private static ?array $currentTenant = null;

    public static function id(): int {
        return (int)($_SESSION['tenant_id'] ?? 1);
    }

    public static function current(): array {
        if (self::$currentTenant === null) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM tenants WHERE id = ? LIMIT 1");
            $stmt->execute([self::id()]);
            $tenant = $stmt->fetch();
            if (!$tenant) {
                // Fallback para o primeiro ativo
                $stmt = $db->query("SELECT * FROM tenants WHERE is_active = 1 ORDER BY id ASC LIMIT 1");
                $tenant = $stmt->fetch();
                if ($tenant) {
                    $_SESSION['tenant_id'] = $tenant['id'];
                } else {
                    $tenant = [
                        'id' => 1,
                        'name' => 'Câmara Municipal',
                        'slug' => 'camara-municipal',
                        'cnpj' => '00.000.000/0001-00',
                        'city' => 'Município',
                        'state' => 'BR',
                        'primary_color' => '#0284c7'
                    ];
                }
            }
            self::$currentTenant = $tenant;
        }
        return self::$currentTenant;
    }

    public static function all(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM tenants WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    }

    public static function switch(int $newTenantId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM tenants WHERE id = ? AND is_active = 1");
        $stmt->execute([$newTenantId]);
        if ($stmt->fetch()) {
            $_SESSION['tenant_id'] = $newTenantId;
            self::$currentTenant = null;
            return true;
        }
        return false;
    }
}
