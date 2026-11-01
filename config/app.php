<?php
/**
 * Câmara Digital - Configurações Gerais da Aplicação
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'Câmara Digital');
define('APP_VERSION', '1.0.0');
define('BASE_URL', '/camara_digital');
define('ROOT_PATH', dirname(__DIR__));

// Autoload para includes e config
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/config/' . $class . '.php',
        ROOT_PATH . '/includes/' . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Require da base do banco
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/Tenant.php';
require_once ROOT_PATH . '/includes/Auth.php';
require_once ROOT_PATH . '/includes/Audit.php';

// Inicializa Tenant na Sessão se não existir
if (!isset($_SESSION['tenant_id'])) {
    $_SESSION['tenant_id'] = 1; // Padrão: Campo Grande
}

// Helpers de URL e Sanitização
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return BASE_URL . ($path !== '' ? '/' . $path : '');
}

function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

function sanitize(?string $str): string {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount): string {
    return 'R$ ' . number_format((float)$amount, 2, ',', '.');
}

function formatDate(?string $date, bool $withTime = false): string {
    if (!$date) return '-';
    $timestamp = strtotime($date);
    if (!$timestamp) return '-';
    return date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
}

function formatCpf(?string $cpf): string {
    $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);
    if (strlen($cpf) !== 11) return $cpf;
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

function formatCnpj(?string $cnpj): string {
    $cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);
    if (strlen($cnpj) !== 14) return $cnpj;
    return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
}

// CSRF Protection
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

// Flash Messages
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
