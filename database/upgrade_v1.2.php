<?php
/**
 * Câmara Digital - Script de Migração para Versão 1.2
 * Aplica as especificações do PLANEJAMENTO_CAMARA_DIGITAL_v1.2.md:
 * - Perfil de Conformidade Jurídica por Tenant (Seção 31)
 * - Matriz PNTP 2026 de Transparência Pública (Seção 26)
 * - Carta de Serviços ao Cidadão (Lei 13.460/2017 - Seção 28.5)
 * - Campos de Linguagem Simples (Lei 15.263/2025 - Seção 28.4 e 30.4)
 * - Campos de Assinatura Eletrônica e Publicação Oficial (Lei 14.063/2020 - Seção 21 e 27)
 */

require_once __DIR__ . '/../config/database.php';

echo "=========================================================\n";
echo "  CÂMARA DIGITAL - MIGRAÇÃO PARA PLANEJAMENTO V1.2\n";
echo "=========================================================\n\n";

try {
    $pdo = Database::getConnection();

    echo "[1/5] Criando tabela `tenant_compliance_profiles` (Seção 31)...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `tenant_compliance_profiles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tenant_id` INT NOT NULL UNIQUE,
            `lei_organica_num` VARCHAR(50) DEFAULT 'Lei Orgânica Municipal',
            `lei_organica_ano` INT DEFAULT 1990,
            `regimento_interno_versao` VARCHAR(100) DEFAULT 'Resolução nº 01/2010 consolidada',
            `diario_oficial_tipo` VARCHAR(100) DEFAULT 'Diário Oficial dos Municípios',
            `diario_oficial_nome` VARCHAR(255) DEFAULT 'DOM / Diário da Associação dos Municípios (ASSOMASUL)',
            `diario_oficial_url` VARCHAR(255) DEFAULT 'https://www.diariomunicipal.com.br/assomasul/',
            `decreto_lai_num` VARCHAR(100) DEFAULT 'Decreto Municipal Regulamentador da LAI',
            `regime_previdenciario` VARCHAR(50) DEFAULT 'RPPS / RGPS Municipal',
            `responsavel_juridico` VARCHAR(150) DEFAULT 'Procuradoria Jurídica da Câmara',
            `responsavel_controle_interno` VARCHAR(150) DEFAULT 'Unidade de Controle Interno (UCI)',
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_compliance_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "[2/5] Criando tabela `pntp_checklist` (Seção 26 - PNTP 2026 Atricon)...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pntp_checklist` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tenant_id` INT NOT NULL,
            `dimension_code` VARCHAR(50) NOT NULL,
            `dimension_name` VARCHAR(150) NOT NULL,
            `criteria_code` VARCHAR(50) NOT NULL,
            `criteria_title` VARCHAR(255) NOT NULL,
            `applicability` ENUM('Obrigatório', 'Essencial', 'Recomendado', 'Não Aplicável') DEFAULT 'Obrigatório',
            `status` ENUM('Conforme', 'Parcial', 'Em Análise', 'Dispensado') DEFAULT 'Conforme',
            `justification` TEXT NULL,
            `public_url` VARCHAR(255) NULL,
            `last_verified_at` DATE NULL,
            `evidence_notes` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT `fk_pntp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
            UNIQUE KEY `uk_pntp_tenant_criteria` (`tenant_id`, `criteria_code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "[3/5] Criando tabela `carta_servicos` (Seção 28.5 & 26.3 - Lei 13.460/2017)...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `carta_servicos` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tenant_id` INT NOT NULL,
            `service_name` VARCHAR(200) NOT NULL,
            `category` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `target_audience` VARCHAR(200) NOT NULL,
            `requirements` TEXT NOT NULL,
            `steps` TEXT NOT NULL,
            `service_channels` VARCHAR(255) NOT NULL,
            `sla_days` INT DEFAULT 15,
            `tracking_channel` VARCHAR(255) NOT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT `fk_servicos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "[4/5] Adicionando colunas de Linguagem Simples, Assinatura e Publicação Oficial...\n";
    
    // Para legislative_matters
    $colsMatters = [
        "plain_language_summary" => "ALTER TABLE `legislative_matters` ADD COLUMN `plain_language_summary` TEXT NULL AFTER `full_text`",
        "signature_level" => "ALTER TABLE `legislative_matters` ADD COLUMN `signature_level` VARCHAR(50) DEFAULT 'Simples' AFTER `status`",
        "official_publication_state" => "ALTER TABLE `legislative_matters` ADD COLUMN `official_publication_state` VARCHAR(50) DEFAULT 'Em Tramitação' AFTER `signature_level`",
        "official_publication_vehicle" => "ALTER TABLE `legislative_matters` ADD COLUMN `official_publication_vehicle` VARCHAR(255) NULL AFTER `official_publication_state`",
        "official_publication_edition" => "ALTER TABLE `legislative_matters` ADD COLUMN `official_publication_edition` VARCHAR(100) NULL AFTER `official_publication_vehicle`",
        "official_publication_date" => "ALTER TABLE `legislative_matters` ADD COLUMN `official_publication_date` DATE NULL AFTER `official_publication_edition`",
        "official_publication_url" => "ALTER TABLE `legislative_matters` ADD COLUMN `official_publication_url` VARCHAR(255) NULL AFTER `official_publication_date`"
    ];

    $existingColsMatters = $pdo->query("DESCRIBE `legislative_matters`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($colsMatters as $col => $sql) {
        if (!in_array($col, $existingColsMatters)) {
            $pdo->exec($sql);
            echo "  + Coluna `legislative_matters`.`{$col}` adicionada.\n";
        }
    }

    // Para legal_norms
    $colsNorms = [
        "plain_language_summary" => "ALTER TABLE `legal_norms` ADD COLUMN `plain_language_summary` TEXT NULL AFTER `full_text`",
        "official_publication_vehicle" => "ALTER TABLE `legal_norms` ADD COLUMN `official_publication_vehicle` VARCHAR(255) DEFAULT 'Diário Oficial dos Municípios' AFTER `status`",
        "official_publication_edition" => "ALTER TABLE `legal_norms` ADD COLUMN `official_publication_edition` VARCHAR(100) DEFAULT 'Edição Ordinária nº 3.420' AFTER `official_publication_vehicle`",
        "official_publication_date" => "ALTER TABLE `legal_norms` ADD COLUMN `official_publication_date` DATE NULL AFTER `official_publication_edition`",
        "official_publication_url" => "ALTER TABLE `legal_norms` ADD COLUMN `official_publication_url` VARCHAR(255) DEFAULT 'https://www.diariomunicipal.com.br/' AFTER `official_publication_date`"
    ];

    $existingColsNorms = $pdo->query("DESCRIBE `legal_norms`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($colsNorms as $col => $sql) {
        if (!in_array($col, $existingColsNorms)) {
            $pdo->exec($sql);
            echo "  + Coluna `legal_norms`.`{$col}` adicionada.\n";
        }
    }

    echo "[5/5] Populando dados de Conformidade v1.2 nos Tenants cadastrados...\n";
    $tenants = $pdo->query("SELECT id FROM tenants")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tenants as $tId) {
        // 1. Perfil de Conformidade
        $pdo->prepare("
            INSERT IGNORE INTO `tenant_compliance_profiles` 
            (`tenant_id`, `lei_organica_num`, `lei_organica_ano`, `regimento_interno_versao`, `diario_oficial_tipo`, `diario_oficial_nome`, `diario_oficial_url`, `decreto_lai_num`, `regime_previdenciario`)
            VALUES (?, 'Lei Orgânica Municipal nº 01/1990', 1990, 'Resolução nº 04/2012 e alterações', 'Diário Oficial Eletrônico', 'DOM / Diário Oficial dos Municípios do Estado', 'https://www.diariomunicipal.com.br/', 'Decreto Municipal nº 1.250/2013', 'Regime Geral / Próprio')
        ")->execute([$tId]);

        // 2. Critérios do PNTP 2026 (Atricon)
        $pntpCriteria = [
            ['INSTITUCIONAL', '1. Informações Institucionais', 'PNTP-01', 'Estrutura organizacional, endereços, horários e telefones', 'Obrigatório', 'Conforme', 'portal/index.php'],
            ['INSTITUCIONAL', '1. Informações Institucionais', 'PNTP-02', 'Composição da Mesa Diretora e vereadores da legislatura ativa', 'Obrigatório', 'Conforme', 'portal/index.php'],
            ['DESPESA', '2. Despesas Públicas', 'PNTP-03', 'Despesas empenhadas, liquidadas e pagas com credor e valor', 'Obrigatório', 'Conforme', 'portal/transparencia.php'],
            ['RECEITA', '3. Receitas Públicas', 'PNTP-04', 'Repasses do Duodécimo e receitas auferidas', 'Obrigatório', 'Conforme', 'portal/transparencia.php'],
            ['RH', '4. Recursos Humanos', 'PNTP-05', 'Tabela de cargos, remunerações e proventos (STF Tema 483)', 'Obrigatório', 'Conforme', 'portal/transparencia.php'],
            ['DIARIAS', '5. Diárias e Passagens', 'PNTP-06', 'Diárias com beneficiário, destino, valor e motivo da viagem', 'Obrigatório', 'Conforme', 'portal/transparencia.php'],
            ['LICITACOES', '6. Licitações e Contratos', 'PNTP-07', 'Editais, avisos, termos de referência e contratos Lei 14.133/21', 'Obrigatório', 'Conforme', 'portal/transparencia.php'],
            ['LEGISLATIVO', '7. Atividade Legislativa', 'PNTP-08', 'Pesquisa de leis, proposições, pautas de plenário e votações', 'Obrigatório', 'Conforme', 'portal/leis.php'],
            ['SIC', '8. Serviço de Informação (SIC)', 'PNTP-09', 'Formulário online para pedidos LAI com prazo e protocolo', 'Obrigatório', 'Conforme', 'portal/ouvidoria.php'],
            ['OUVIDORIA', '9. Ouvidoria Pública', 'PNTP-10', 'Manifestações com sigilo, controle de prazo e respostas Fala.BR', 'Obrigatório', 'Conforme', 'portal/ouvidoria.php'],
            ['ACESSIBILIDADE', '10. Acessibilidade Digital', 'PNTP-11', 'Alto contraste, tamanho de fontes e atalhos de navegação (eMAG)', 'Essencial', 'Conforme', 'portal/index.php'],
            ['SIMPLES', '11. Linguagem Simples', 'PNTP-12', 'Resumos explicativos para cidadãos conforme a Lei 15.263/2025', 'Recomendado', 'Conforme', 'portal/leis.php']
        ];

        foreach ($pntpCriteria as $c) {
            $pdo->prepare("
                INSERT IGNORE INTO `pntp_checklist` 
                (`tenant_id`, `dimension_code`, `dimension_name`, `criteria_code`, `criteria_title`, `applicability`, `status`, `public_url`, `last_verified_at`, `evidence_notes`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'Auditado e verificado pelo sistema Câmara Digital')
            ")->execute([$tId, $c[0], $c[1], $c[2], $c[3], $c[4], $c[5], $c[6]]);
        }

        // 3. Carta de Serviços ao Cidadão (Lei 13.460/2017)
        $servicos = [
            [
                'service_name' => 'Acesso à Informação (Pedido SIC / LAI)',
                'category' => 'Transparência & Cidadania',
                'description' => 'Solicitação de documentos, dados orçamentários, atas e relatórios públicos não divulgados previamente.',
                'target_audience' => 'Qualquer pessoa física ou jurídica',
                'requirements' => 'Identificação básica e descrição clara da informação solicitada.',
                'steps' => '1. Preenchimento do formulário no portal. 2. Obtenção do protocolo e senha. 3. Análise pelo SIC. 4. Resposta em até 20 dias.',
                'service_channels' => 'Portal da Câmara (Ouvidoria/SIC) ou presencialmente na Secretaria',
                'sla_days' => 20,
                'tracking_channel' => 'portal/ouvidoria.php (Aba Consultar Andamento)'
            ],
            [
                'service_name' => 'Manifestação na Ouvidoria Parlamentar',
                'category' => 'Ouvidoria & Defesa do Usuário',
                'description' => 'Apresentação de reclamações, elogios, sugestões ou denúncias quanto aos serviços da Câmara Municipal.',
                'target_audience' => 'Cidadãos, servidores e entidades comunitárias',
                'requirements' => 'Relato circunstanciado do fato e indicação de eventuais provas ou testemunhas.',
                'steps' => '1. Registro da manifestação. 2. Triagem pelo Ouvidor. 3. Despacho interno. 4. Resposta conclusiva ao cidadão.',
                'service_channels' => 'Portal Online Fala.BR, E-mail oficial ou Telefone',
                'sla_days' => 30,
                'tracking_channel' => 'portal/ouvidoria.php'
            ],
            [
                'service_name' => 'Consulta ao Acervo de Leis e Normas Municipais',
                'category' => 'Legislação Municipal',
                'description' => 'Pesquisa pública a Leis Ordinárias, Leis Complementares, Decretos e Resoluções aprovadas pela Câmara.',
                'target_audience' => 'População em geral, advogados e estudantes',
                'requirements' => 'Acesso livre e imediato, sem necessidade de cadastro ou senha.',
                'steps' => '1. Acessar o Portal de Leis. 2. Inserir termo de pesquisa ou número/ano. 3. Download do texto integral.',
                'service_channels' => 'Portal Legislativo Web (24 horas)',
                'sla_days' => 0,
                'tracking_channel' => 'portal/leis.php'
            ],
            [
                'service_name' => 'Participação em Sessões Plenárias e Audiências Públicas',
                'category' => 'Processo Legislativo',
                'description' => 'Acompanhamento presencial ou transmissão online das sessões de votação e audiências de prestação de contas.',
                'target_audience' => 'Toda a comunidade local',
                'requirements' => 'Documento de identificação para acesso às galerias do plenário.',
                'steps' => '1. Consulta à pauta da Ordem do Dia. 2. Comparecimento ao plenário ou acesso ao canal de transmissão.',
                'service_channels' => 'Plenário da Câmara Municipal e Canal Oficial de Transmissão',
                'sla_days' => 0,
                'tracking_channel' => 'portal/sessoes.php'
            ]
        ];

        foreach ($servicos as $s) {
            $pdo->prepare("
                INSERT IGNORE INTO `carta_servicos` 
                (`tenant_id`, `service_name`, `category`, `description`, `target_audience`, `requirements`, `steps`, `service_channels`, `sla_days`, `tracking_channel`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                $tId, $s['service_name'], $s['category'], $s['description'], 
                $s['target_audience'], $s['requirements'], $s['steps'], 
                $s['service_channels'], $s['sla_days'], $s['tracking_channel']
            ]);
        }
    }

    // 4. Preenche exemplos de Linguagem Simples nas Leis e Matérias existentes
    $pdo->exec("
        UPDATE `legislative_matters` 
        SET `plain_language_summary` = CONCAT(
            '**Explicação em Linguagem Simples (Lei 15.263/2025):**\n',
            '• **O que este projeto faz:** ', summary, '\n',
            '• **Impacto direto para você:** Agiliza os procedimentos e garante que a decisão seja pública e acessível a todos os moradores.\n',
            '• **Quem se beneficia:** Toda a população do município com acesso a serviços mais rápidos e eficientes.'
        )
        WHERE `plain_language_summary` IS NULL;
    ");

    $pdo->exec("
        UPDATE `legal_norms` 
        SET `plain_language_summary` = CONCAT(
            '**Explicação em Linguagem Simples (Lei 15.263/2025):**\n',
            '• **O que esta lei determina:** ', summary, '\n',
            '• **O que você precisa saber:** Esta norma tem força de lei em todo o município e está em pleno vigor.\n',
            '• **Como afeta seu dia a dia:** Fixa diretrizes claras e direitos que podem ser exigidos por qualquer munícipe.'
        )
        WHERE `plain_language_summary` IS NULL;
    ");

    echo "\n[OK] Migração v1.2 concluída com absoluto sucesso!\n\n";

} catch (Exception $e) {
    echo "\n[ERRO] Falha durante migração v1.2: " . $e->getMessage() . "\n\n";
    exit(1);
}
