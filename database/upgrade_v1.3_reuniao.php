<?php
/**
 * Câmara Digital - Migração v1.3 (Backlog da Reunião de 30/10/2026)
 * - Licenciamento e Trava Anti-Cópia (Vercel License Server Integration)
 * - Matriz Dinâmica de Permissões por Perfil (RBAC por Módulo pelo Admin Geral)
 * - Unicidade de Voto Nominal em Plenário
 * - Direcionamento de Ouvidoria para Vereador Específico
 * - Detalhamento e Conteúdo Integral de Protocolos ("Em Análise")
 * - Histórico de Manutenção de Frota
 */

require_once __DIR__ . '/../config/database.php';

echo "=================================================================\n";
echo "  CÂMARA DIGITAL - MIGRAÇÃO V1.3 (MELHORIAS DA REUNIÃO)\n";
echo "=================================================================\n\n";

try {
    $pdo = Database::getConnection();

    // 1. Tabela de Licenciamento e Trava Anti-Cópia (Sincronizada com Vercel)
    echo "[1/6] Criando tabela `system_licenses` (Controle de Assinatura e Anti-Cópia)...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `system_licenses` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tenant_id` INT NOT NULL UNIQUE,
            `license_key` VARCHAR(120) NOT NULL,
            `plan_name` VARCHAR(80) DEFAULT 'Enterprise Municipal',
            `status` ENUM('active', 'grace_period', 'suspended', 'blocked_copy') DEFAULT 'active',
            `allowed_domain` VARCHAR(255) DEFAULT 'localhost',
            `server_fingerprint` VARCHAR(64) NOT NULL,
            `expires_at` DATE NOT NULL,
            `vercel_api_url` VARCHAR(255) DEFAULT 'https://camara-license-server.vercel.app/api/verify',
            `last_heartbeat_at` DATETIME NULL,
            `signature_hmac` VARCHAR(128) NULL,
            `block_reason` VARCHAR(255) NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_license_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Tabela de Permissões de Módulos por Tipo de Usuário (Gerenciada pelo Admin Geral da Câmara)
    echo "[2/6] Criando tabela `role_module_permissions` (RBAC por Módulo)...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `role_module_permissions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tenant_id` INT NOT NULL,
            `role_id` INT NOT NULL,
            `module_slug` VARCHAR(50) NOT NULL,
            `can_access` TINYINT(1) NOT NULL DEFAULT 1,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_tenant_role_mod` (`tenant_id`, `role_id`, `module_slug`),
            CONSTRAINT `fk_rmp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_rmp_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Unicidade de Voto em session_votes
    echo "[3/6] Garantindo índice UNIQUE em `session_votes` (agenda_item_id, councilor_id)...\n";
    // Remove eventuais duplicatas antes de criar o índice único
    $pdo->exec("
        DELETE sv1 FROM session_votes sv1
        INNER JOIN session_votes sv2 
        WHERE sv1.id < sv2.id 
          AND sv1.agenda_item_id = sv2.agenda_item_id 
          AND sv1.councilor_id = sv2.councilor_id
    ");
    $indexes = $pdo->query("SHOW INDEX FROM `session_votes` WHERE Key_name = 'uk_vote_item_councilor'")->fetchAll();
    if (empty($indexes)) {
        $pdo->exec("ALTER TABLE `session_votes` ADD UNIQUE KEY `uk_vote_item_councilor` (`agenda_item_id`, `councilor_id`)");
        echo "  + Índice único `uk_vote_item_councilor` criado em `session_votes`.\n";
    }

    // 4. Coluna target_councilor_id e neighborhood em citizen_requests (Direcionamento para Vereador)
    echo "[4/6] Atualizando `citizen_requests` para direcionamento a vereadores...\n";
    $colsCitizen = $pdo->query("DESCRIBE `citizen_requests`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('target_councilor_id', $colsCitizen)) {
        $pdo->exec("ALTER TABLE `citizen_requests` ADD COLUMN `target_councilor_id` INT NULL AFTER `request_type`");
        echo "  + Coluna `target_councilor_id` adicionada em `citizen_requests`.\n";
    }
    if (!in_array('neighborhood', $colsCitizen)) {
        $pdo->exec("ALTER TABLE `citizen_requests` ADD COLUMN `neighborhood` VARCHAR(120) NULL AFTER `citizen_phone`");
        echo "  + Coluna `neighborhood` adicionada em `citizen_requests`.\n";
    }

    // 5. Coluna full_content em protocols e status Em Análise
    echo "[5/6] Atualizando `protocols` para visualização completa de documentos e status 'Em Análise'...\n";
    $colsProtocols = $pdo->query("DESCRIBE `protocols`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('full_content', $colsProtocols)) {
        $pdo->exec("ALTER TABLE `protocols` ADD COLUMN `full_content` LONGTEXT NULL AFTER `summary`");
        echo "  + Coluna `full_content` adicionada em `protocols`.\n";
    }
    $pdo->exec("
        UPDATE `protocols` 
        SET `full_content` = CONCAT(
            'EXPEDIENTE OFICIAL PROTOCOLADO Nº ', protocol_number, '\n',
            'REMETENTE: ', sender_name, ' (', sender_type, ')\n',
            'ASSUNTO: ', subject, '\n',
            'SETOR ATUAL: ', destination_department, '\n\n',
            'TEOR INTEGRAL DO DOCUMENTO:\n',
            summary, '\n\n',
            'Documento assinado eletronicamente e registrado no Livro Digital da Câmara Municipal com hash de integridade SHA-256: ', verification_hash
        )
        WHERE `full_content` IS NULL
    ");
    // Garante que haja pelo menos um protocolo com status 'Em Análise' para demonstração
    $pdo->exec("UPDATE `protocols` SET `status` = 'Em Análise' WHERE `status` = 'Aberto' LIMIT 1");

    // 6. Tabela de Histórico de Manutenção de Veículos e dados iniciais de Licença e Permissões
    echo "[6/6] Criando tabela `vehicle_maintenances` e populando Permissões e Licenças...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `vehicle_maintenances` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tenant_id` INT NOT NULL,
            `vehicle_id` INT NOT NULL,
            `maintenance_date` DATE NOT NULL,
            `maintenance_type` VARCHAR(80) NOT NULL DEFAULT 'Preventiva / Revisão',
            `description` TEXT NOT NULL,
            `cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `mileage_at_service` INT NOT NULL DEFAULT 0,
            `workshop_name` VARCHAR(150) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT `fk_vm_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_vm_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Popula matriz padrão de permissões por perfil para cada tenant
    $tenants = $pdo->query("SELECT id, slug, cnpj FROM tenants")->fetchAll();
    $roles = $pdo->query("SELECT id, slug FROM roles")->fetchAll();

    $defaultRoleModules = [
        'admin'        => ['administracao', 'legislativo', 'rh', 'almoxarifado', 'compras', 'patrimonio', 'financeiro', 'transparencia', 'ouvidoria', 'protocolo', 'controle_interno', 'ia_legislativa', 'noticias', 'backup'],
        'mesa'         => ['administracao', 'legislativo', 'transparencia', 'ouvidoria', 'protocolo', 'controle_interno', 'ia_legislativa', 'noticias'],
        'vereador'     => ['legislativo', 'transparencia', 'ouvidoria', 'protocolo', 'ia_legislativa'],
        'secretaria'   => ['administracao', 'legislativo', 'transparencia', 'protocolo', 'ia_legislativa', 'noticias'],
        'rh'           => ['rh', 'transparencia', 'protocolo'],
        'almoxarifado' => ['almoxarifado', 'patrimonio', 'protocolo'],
        'compras'      => ['compras', 'almoxarifado', 'patrimonio', 'transparencia', 'protocolo'],
        'financeiro'   => ['financeiro', 'compras', 'transparencia', 'protocolo'],
        'controle'     => ['administracao', 'legislativo', 'rh', 'almoxarifado', 'compras', 'patrimonio', 'financeiro', 'transparencia', 'ouvidoria', 'protocolo', 'controle_interno'],
        'ouvidoria'    => ['ouvidoria', 'transparencia', 'protocolo'],
        'comunicacao'  => ['noticias', 'transparencia', 'legislativo']
    ];

    $allModules = ['administracao', 'legislativo', 'rh', 'almoxarifado', 'compras', 'patrimonio', 'financeiro', 'transparencia', 'ouvidoria', 'protocolo', 'controle_interno', 'ia_legislativa', 'noticias', 'backup'];

    $stmtPerm = $pdo->prepare("
        INSERT IGNORE INTO `role_module_permissions` (`tenant_id`, `role_id`, `module_slug`, `can_access`)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($tenants as $t) {
        $tId = (int)$t['id'];
        foreach ($roles as $r) {
            $rId = (int)$r['id'];
            $rSlug = $r['slug'];
            $allowedList = $defaultRoleModules[$rSlug] ?? ['legislativo', 'protocolo'];
            foreach ($allModules as $mod) {
                $can = in_array($mod, $allowedList, true) ? 1 : 0;
                $stmtPerm->execute([$tId, $rId, $mod, $can]);
            }
        }

        // Cria licença padrão ativa vinculada ao fingerprint local
        $fingerprint = hash('sha256', 'CAMARA_FP|' . php_uname('n') . '|' . dirname(__DIR__) . '|' . $t['cnpj']);
        $licenseKey = 'LIC-' . strtoupper(substr(md5($t['slug'] . $t['cnpj']), 0, 8)) . '-2026-PRO';
        $expiresAt = date('Y-m-d', strtotime('+365 days'));
        $secret = 'CAMARA_DEV_MASTER_SECRET_2026';
        $hmac = hash_hmac('sha256', $licenseKey . '|active|' . $expiresAt . '|' . $fingerprint, $secret);

        $stmtLic = $pdo->prepare("
            INSERT IGNORE INTO `system_licenses`
            (`tenant_id`, `license_key`, `plan_name`, `status`, `allowed_domain`, `server_fingerprint`, `expires_at`, `last_heartbeat_at`, `signature_hmac`)
            VALUES (?, ?, 'Assinatura Anual Enterprise + IA', 'active', 'localhost', ?, ?, NOW(), ?)
        ");
        $stmtLic->execute([$tId, $licenseKey, $fingerprint, $expiresAt, $hmac]);
    }

    // Garante usuário de Comunicação Social para teste rápido no login
    $pdo->exec("
        INSERT IGNORE INTO `users` (`id`, `tenant_id`, `role_id`, `name`, `email`, `password_hash`, `cpf`, `job_title`, `department`, `is_active`)
        VALUES (11, 1, 11, 'Juliana Rios (ASCOM)', 'comunicacao@camara.ms.gov.br', '\$2y\$10\$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '234.987.123-45', 'Assessora de Comunicação', 'Assessoria de Comunicação', 1)
    ");

    // Vincula os demais vereadores a usuários para teste de votação individual se necessário
    echo "\n[OK] Migração v1.3 executada com sucesso!\n";

} catch (Exception $e) {
    echo "\n[ERRO] " . $e->getMessage() . "\n";
    exit(1);
}

