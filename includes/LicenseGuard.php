<?php
/**
 * Câmara Digital - Guardião de Licenciamento, Assinatura e Proteção Anti-Cópia
 * Conecta-se ao Servidor de Licenças na Vercel (/api/verify) e valida assinatura HMAC-SHA256
 * e vínculo físico de servidor (Server Fingerprint) para impedir cópias não autorizadas.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Tenant.php';
require_once __DIR__ . '/Audit.php';

class LicenseGuard {
    // Segredo criptográfico compartilhado exclusivamente com a Serverless Function na Vercel
    private const DEV_MASTER_SECRET = 'CAMARA_DEV_MASTER_SECRET_2026';
    private const DEV_ADMIN_PIN = 'admin2026';

    /**
     * Calcula a impressão digital (Fingerprint) única do servidor + diretório + CNPJ
     * Se os arquivos forem copiados para outra máquina ou domínio, o hash muda imediatamente.
     */
    public static function getServerFingerprint(?string $cnpj = null): string {
        if ($cnpj === null) {
            $tenant = Tenant::current();
            $cnpj = $tenant['cnpj'] ?? '00.000.000/0001-00';
        }
        $host = php_uname('n');
        $root = dirname(__DIR__);
        return hash('sha256', 'CAMARA_FP|' . $host . '|' . $root . '|' . $cnpj);
    }

    /**
     * Assina os parâmetros da licença usando HMAC-SHA256
     * Impede adulteração direta no banco de dados MySQL pelo cliente.
     */
    public static function signPayload(string $licenseKey, string $status, string $expiresAt, string $fingerprint): string {
        $payload = "{$licenseKey}|{$status}|{$expiresAt}|{$fingerprint}";
        return hash_hmac('sha256', $payload, self::DEV_MASTER_SECRET);
    }

    /**
     * Obtém os dados da licença do Tenant atual
     */
    public static function getLicense(?int $tenantId = null): array {
        $tenantId = $tenantId ?? Tenant::id();
        $tenant = Tenant::current();
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM system_licenses WHERE tenant_id = ? LIMIT 1");
        $stmt->execute([$tenantId]);
        $lic = $stmt->fetch();

        if (!$lic) {
            $fp = self::getServerFingerprint($tenant['cnpj']);
            $key = 'LIC-' . strtoupper(substr(md5(($tenant['slug'] ?? 'camara') . $tenant['cnpj']), 0, 8)) . '-2026-PRO';
            $exp = date('Y-m-d', strtotime('+365 days'));
            $hmac = self::signPayload($key, 'active', $exp, $fp);

            $ins = $db->prepare("
                INSERT INTO system_licenses 
                (tenant_id, license_key, plan_name, status, allowed_domain, server_fingerprint, expires_at, last_heartbeat_at, signature_hmac)
                VALUES (?, ?, 'Assinatura Anual Enterprise + IA', 'active', 'localhost', ?, ?, NOW(), ?)
            ");
            $ins->execute([$tenantId, $key, $fp, $exp, $hmac]);

            $stmt->execute([$tenantId]);
            $lic = $stmt->fetch();
        }

        return $lic;
    }

    /**
     * Verifica se a licença está válida, íntegra (HMAC) e rodando no servidor autorizado
     */
    public static function check(?int $tenantId = null): array {
        $lic = self::getLicense($tenantId);
        $currentFp = self::getServerFingerprint();

        // 1. Verifica integridade criptográfica HMAC (evita edição manual na tabela MySQL)
        $expectedHmac = self::signPayload(
            $lic['license_key'],
            $lic['status'],
            $lic['expires_at'],
            $lic['server_fingerprint']
        );

        if (!empty($lic['signature_hmac']) && !hash_equals($expectedHmac, $lic['signature_hmac'])) {
            return [
                'valid' => false,
                'status' => 'blocked_copy',
                'reason' => 'Violação de integridade detectada: a tabela de licenciamento foi alterada sem assinatura criptográfica do desenvolvedor.',
                'license' => $lic,
                'current_fingerprint' => $currentFp
            ];
        }

        // 2. Verifica vínculo de Hardware / Servidor (Trava Anti-Cópia)
        if ($lic['server_fingerprint'] !== 'ANY' && !hash_equals($lic['server_fingerprint'], $currentFp)) {
            return [
                'valid' => false,
                'status' => 'blocked_copy',
                'reason' => 'Trava Anti-Cópia Acionada: Esta cópia do código-fonte está vinculada a outro servidor físico. Solicite autorização de migração ao desenvolvedor.',
                'license' => $lic,
                'current_fingerprint' => $currentFp
            ];
        }

        // 3. Verifica status de bloqueio por inadimplência (Kill-switch)
        if ($lic['status'] === 'suspended' || $lic['status'] === 'blocked_copy') {
            return [
                'valid' => false,
                'status' => $lic['status'],
                'reason' => $lic['block_reason'] ?: 'Acesso suspenso temporariamente por pendência contratual/financeira. Entre em contato com o suporte comercial do desenvolvedor.',
                'license' => $lic,
                'current_fingerprint' => $currentFp
            ];
        }

        // 4. Verifica data de expiração
        if ($lic['expires_at'] < date('Y-m-d')) {
            return [
                'valid' => false,
                'status' => 'suspended',
                'reason' => 'Licença anual expirada em ' . formatDate($lic['expires_at']) . '. Renove sua assinatura no portal Vercel.',
                'license' => $lic,
                'current_fingerprint' => $currentFp
            ];
        }

        return [
            'valid' => true,
            'status' => $lic['status'],
            'reason' => null,
            'license' => $lic,
            'current_fingerprint' => $currentFp
        ];
    }

    /**
     * Sincroniza com o Servidor de Licenças na Vercel via HTTP POST (/api/verify)
     */
    public static function syncWithVercel(?int $tenantId = null): array {
        $tenantId = $tenantId ?? Tenant::id();
        $tenant = Tenant::current();
        $lic = self::getLicense($tenantId);
        $currentFp = self::getServerFingerprint($tenant['cnpj']);
        $apiUrl = trim($lic['vercel_api_url'] ?? '');

        if (empty($apiUrl)) {
            return ['synced' => false, 'message' => 'URL da API Vercel não configurada.'];
        }

        $payload = json_encode([
            'license_key' => $lic['license_key'],
            'cnpj' => $tenant['cnpj'],
            'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
            'server_fingerprint' => $currentFp
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content' => $payload,
                'timeout' => 3,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $response = @file_get_contents($apiUrl, false, $ctx);
        if ($response === false) {
            // Atualiza timestamp de tentativa local sem derrubar o cliente se a internet oscilar
            return [
                'synced' => false,
                'message' => 'Servidor Vercel inacessível no momento (Operando com validação criptográfica local HMAC-SHA256).'
            ];
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['status']) || empty($data['signature_hmac'])) {
            return ['synced' => false, 'message' => 'Resposta inválida do servidor de licenças.'];
        }

        // Verifica se a resposta realmente veio do servidor Vercel do Desenvolvedor
        $remoteFp = $data['server_fingerprint'] ?? $currentFp;
        $expectedHmac = self::signPayload($lic['license_key'], $data['status'], $data['expires_at'] ?? $lic['expires_at'], $remoteFp);

        if (!hash_equals($expectedHmac, $data['signature_hmac'])) {
            return ['synced' => false, 'message' => 'Assinatura HMAC do servidor remoto inválida.'];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE system_licenses 
            SET status = ?, expires_at = ?, server_fingerprint = ?, block_reason = ?, signature_hmac = ?, last_heartbeat_at = NOW()
            WHERE tenant_id = ?
        ");
        $stmt->execute([
            $data['status'],
            $data['expires_at'] ?? $lic['expires_at'],
            $remoteFp,
            $data['block_reason'] ?? null,
            $data['signature_hmac'],
            $tenantId
        ]);

        return [
            'synced' => true,
            'status' => $data['status'],
            'message' => 'Sincronizado com sucesso com o servidor Vercel Guard!'
        ];
    }

    /**
     * Aplica um Token Criptográfico Base64 gerado no Painel Vercel do Desenvolvedor
     */
    public static function applySignedToken(string $base64Token, ?int $tenantId = null): array {
        $tenantId = $tenantId ?? Tenant::id();
        $decoded = base64_decode(trim($base64Token), true);
        if (!$decoded) {
            return ['success' => false, 'error' => 'Formato de token Base64 inválido.'];
        }

        $data = json_decode($decoded, true);
        if (!is_array($data) || empty($data['license_key']) || empty($data['status']) || empty($data['expires_at']) || empty($data['signature_hmac'])) {
            return ['success' => false, 'error' => 'Estrutura do token de licença incompleta.'];
        }

        $fp = $data['server_fingerprint'] ?? self::getServerFingerprint();
        if ($fp === 'ANY') {
            // Verifica HMAC assinado com 'ANY' e depois vincula ao fingerprint atual do servidor
            $expectedAny = self::signPayload($data['license_key'], $data['status'], $data['expires_at'], 'ANY');
            if (!hash_equals($expectedAny, $data['signature_hmac'])) {
                return ['success' => false, 'error' => 'Assinatura criptográfica HMAC-SHA256 inválida! Token recusado.'];
            }
            $fp = self::getServerFingerprint();
            $finalHmac = self::signPayload($data['license_key'], $data['status'], $data['expires_at'], $fp);
        } else {
            $expected = self::signPayload($data['license_key'], $data['status'], $data['expires_at'], $fp);
            if (!hash_equals($expected, $data['signature_hmac'])) {
                return ['success' => false, 'error' => 'Assinatura criptográfica HMAC-SHA256 inválida! Token recusado.'];
            }
            $finalHmac = $data['signature_hmac'];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE system_licenses 
            SET license_key = ?, status = ?, expires_at = ?, server_fingerprint = ?, block_reason = ?, signature_hmac = ?, last_heartbeat_at = NOW()
            WHERE tenant_id = ?
        ");
        $stmt->execute([
            $data['license_key'],
            $data['status'],
            $data['expires_at'],
            $fp,
            $data['block_reason'] ?? null,
            $finalHmac,
            $tenantId
        ]);

        Audit::log('LICENCA_ATUALIZADA', 'system_licenses', $tenantId, "Token criptográfico aplicado. Novo status: {$data['status']}");

        return ['success' => true, 'status' => $data['status']];
    }

    /**
     * Ação rápida do Desenvolvedor (protegida por PIN) para testar ou acionar a trava
     */
    public static function devOverride(string $pin, string $newStatus, ?string $reason = null, ?string $vercelUrl = null, ?int $tenantId = null): array {
        if ($pin !== self::DEV_ADMIN_PIN) {
            return ['success' => false, 'error' => 'PIN de Desenvolvedor incorreto.'];
        }

        $tenantId = $tenantId ?? Tenant::id();
        $lic = self::getLicense($tenantId);
        $fp = ($newStatus === 'blocked_copy') ? 'FINGERPRINT_SERVIDOR_ORIGEM_DIVERGENTE' : self::getServerFingerprint();
        $exp = $lic['expires_at'];
        if ($newStatus === 'active' && $exp < date('Y-m-d')) {
            $exp = date('Y-m-d', strtotime('+365 days'));
        }
        $hmac = self::signPayload($lic['license_key'], $newStatus, $exp, $fp);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE system_licenses 
            SET status = ?, expires_at = ?, server_fingerprint = ?, block_reason = ?, signature_hmac = ?, vercel_api_url = COALESCE(?, vercel_api_url), last_heartbeat_at = NOW()
            WHERE tenant_id = ?
        ");
        $stmt->execute([$newStatus, $exp, $fp, $reason, $hmac, $vercelUrl, $tenantId]);

        Audit::log('DEV_KILLSWITCH', 'system_licenses', $tenantId, "Kill-switch alterado pelo Desenvolvedor para: {$newStatus}");
        return ['success' => true, 'status' => $newStatus];
    }

    /**
     * Executado em todas as páginas administrativas para bloquear o sistema caso suspenso ou copiado
     */
    public static function enforce(): void {
        $scriptName = basename($_SERVER['SCRIPT_NAME'] ?? '');
        if (in_array($scriptName, ['licenca_bloqueada.php', 'login.php', 'logout.php'], true)) {
            return;
        }

        $check = self::check();
        if (!$check['valid']) {
            header('Location: ' . url('licenca_bloqueada.php'));
            exit;
        }
    }
}

