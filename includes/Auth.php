<?php
/**
 * Câmara Digital - Gerenciamento de Autenticação e RBAC Dinâmico por Módulo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Tenant.php';
require_once __DIR__ . '/Audit.php';

class Auth {
    private static ?array $user = null;
    private static ?array $allowedModules = null;

    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        if (self::$user === null) {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT u.*, r.slug as role_slug, r.name as role_name, r.level as role_level,
                       c.id as linked_councilor_id, c.full_name as councilor_name, c.political_party, c.board_role
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                LEFT JOIN councilors c ON c.user_id = u.id AND c.tenant_id = u.tenant_id
                WHERE u.id = ? AND u.is_active = 1 
                LIMIT 1
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            if ($user) {
                unset($user['password_hash']);
                self::$user = $user;
            } else {
                self::logout();
            }
        }
        return self::$user;
    }

    /**
     * Retorna o ID do Vereador vinculado ao usuário logado (se for parlamentar)
     */
    public static function councilorId(): ?int {
        $user = self::user();
        if (!$user) return null;
        if (!empty($user['linked_councilor_id'])) {
            return (int)$user['linked_councilor_id'];
        }
        return null;
    }

    public static function hasRole($roles): bool {
        $user = self::user();
        if (!$user) return false;

        // Administrador geral tem permissão total
        if ($user['role_slug'] === 'admin') return true;

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($user['role_slug'], $roles, true);
    }

    /**
     * Verifica na matriz configurada pelo Administrador Geral da Câmara
     * se o perfil (role) do usuário atual pode acessar determinado módulo.
     */
    public static function canAccessModule(string $moduleSlug): bool {
        $user = self::user();
        if (!$user) return false;

        // Administrador Geral da Câmara sempre tem acesso irrestrito
        if ($user['role_slug'] === 'admin') {
            return true;
        }

        if (self::$allowedModules === null) {
            $db = Database::getConnection();
            $tenantId = Tenant::id();
            $stmt = $db->prepare("
                SELECT module_slug, can_access 
                FROM role_module_permissions 
                WHERE tenant_id = ? AND role_id = ?
            ");
            $stmt->execute([$tenantId, $user['role_id']]);
            $rows = $stmt->fetchAll();

            self::$allowedModules = [];
            foreach ($rows as $row) {
                if ((int)$row['can_access'] === 1) {
                    self::$allowedModules[] = $row['module_slug'];
                }
            }
        }

        return in_array($moduleSlug, self::$allowedModules, true);
    }

    public static function requireModuleAccess(string $moduleSlug): void {
        self::requireLogin();
        if (!self::canAccessModule($moduleSlug)) {
            $user = self::user();
            http_response_code(403);
            die("
                <div style='font-family:sans-serif; max-width:520px; margin:80px auto; text-align:center; padding:40px; border:1px solid #fecdd3; background:#fff1f2; border-radius:16px;'>
                    <h2 style='color:#be123c; margin-top:0;'>403 — Módulo Restrito para seu Perfil</h2>
                    <p style='color:#475569; font-size:14px; line-height:1.6;'>
                        Seu perfil de acesso atual (<strong>" . htmlspecialchars($user['role_name'] ?? '') . "</strong>) foi limitado pelo Administrador Geral da Câmara e não possui permissão para acessar o módulo <code>" . htmlspecialchars($moduleSlug) . "</code>.
                    </p>
                    <a href='" . url('dashboard.php') . "' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#0284c7; color:#fff; text-decoration:none; border-radius:8px; font-weight:bold; font-size:13px;'>Voltar ao Painel Geral</a>
                </div>
            ");
        }
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '/camara_digital/dashboard.php';
            header("Location: " . url('login.php'));
            exit;
        }
    }

    public static function requireRole($roles): void {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            die("
                <div style='font-family:sans-serif; text-align:center; padding:50px;'>
                    <h2 style='color:#dc2626;'>403 - Acesso Negado</h2>
                    <p>Seu perfil de acesso atual não possui permissão para acessar esta área.</p>
                    <a href='" . url('dashboard.php') . "' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#0284c7; color:#fff; text-decoration:none; border-radius:6px;'>Voltar ao Painel</a>
                </div>
            ");
        }
    }

    public static function login(string $email, string $password): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, r.slug as role_slug 
            FROM users u 
            INNER JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND u.is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            self::$user = null;
            self::$allowedModules = null;

            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            Audit::log('LOGIN', 'users', $user['id'], 'Login efetuado com sucesso via formulário.');
            return true;
        }
        return false;
    }

    public static function loginAs(int $userId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, r.slug as role_slug 
            FROM users u 
            INNER JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ? AND u.is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            self::$user = null;
            self::$allowedModules = null;

            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            Audit::log('LOGIN_QUICK', 'users', $user['id'], 'Login rápido executado como: ' . $user['name']);
            return true;
        }
        return false;
    }

    public static function logout(): void {
        if (self::check()) {
            Audit::log('LOGOUT', 'users', self::id(), 'Sessão encerrada pelo usuário.');
        }
        unset($_SESSION['user_id']);
        self::$user = null;
        self::$allowedModules = null;
        session_regenerate_id(true);
    }
}
