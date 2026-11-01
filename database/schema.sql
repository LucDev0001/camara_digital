-- ==============================================================
-- CÂMARA DIGITAL - BANCO DE DADOS RELACIONAL MYSQL
-- Multi-Tenant SaaS para Gestão Integrada de Câmaras Municipais
-- ==============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. TENANTS (Câmaras Municipais)
CREATE TABLE IF NOT EXISTS `tenants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `cnpj` VARCHAR(20) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(2) NOT NULL DEFAULT 'MS',
    `email` VARCHAR(150),
    `phone` VARCHAR(50),
    `address` VARCHAR(255),
    `coat_of_arms_url` VARCHAR(255),
    `primary_color` VARCHAR(20) DEFAULT '#0f3460',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. ROLES (Papéis de Usuários)
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `description` VARCHAR(255),
    `level` INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. USERS (Usuários do Sistema)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `role_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `cpf` VARCHAR(20),
    `job_title` VARCHAR(100),
    `department` VARCHAR(100),
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. DEPARTMENTS (Departamentos / Setores)
CREATE TABLE IF NOT EXISTS `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `code` VARCHAR(50),
    `manager_name` VARCHAR(150),
    `email` VARCHAR(150),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_dept_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. LEGISLATURES (Legislaturas)
CREATE TABLE IF NOT EXISTS `legislatures` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `number` INT NOT NULL,
    `start_year` INT NOT NULL,
    `end_year` INT NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_leg_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. COUNCILORS (Vereadores)
CREATE TABLE IF NOT EXISTS `councilors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `user_id` INT NULL,
    `legislature_id` INT NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `political_party` VARCHAR(50) NOT NULL,
    `board_role` VARCHAR(50) DEFAULT 'Membro',
    `photo_url` VARCHAR(255),
    `biography` TEXT,
    `email` VARCHAR(150),
    `phone` VARCHAR(50),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_counc_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_counc_leg` FOREIGN KEY (`legislature_id`) REFERENCES `legislatures`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. COMMITTEES (Comissões Permanentes e Especiais)
CREATE TABLE IF NOT EXISTS `committees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `president_name` VARCHAR(150),
    `rapporteur_name` VARCHAR(150),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_com_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. LEGISLATIVE MATTERS (Proposições e Matérias Legislativas)
CREATE TABLE IF NOT EXISTS `legislative_matters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `number` INT NOT NULL,
    `year` INT NOT NULL,
    `type` VARCHAR(80) NOT NULL,
    `author_councilor_id` INT NULL,
    `author_name` VARCHAR(150) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `summary` TEXT NOT NULL,
    `full_text` LONGTEXT,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Em Tramitação',
    `urgency` VARCHAR(50) NOT NULL DEFAULT 'Normal',
    `presentation_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_matters_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_matters_author` FOREIGN KEY (`author_councilor_id`) REFERENCES `councilors`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. MATTER MOVEMENTS (Tramitações)
CREATE TABLE IF NOT EXISTS `matter_movements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `matter_id` INT NOT NULL,
    `from_dept` VARCHAR(100),
    `to_dept` VARCHAR(100) NOT NULL,
    `dispatch_text` TEXT NOT NULL,
    `status_after` VARCHAR(50) NOT NULL,
    `movement_date` DATETIME NOT NULL,
    `user_name` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_move_matter` FOREIGN KEY (`matter_id`) REFERENCES `legislative_matters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. SESSIONS (Sessões Plenárias)
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `legislature_id` INT NOT NULL,
    `session_number` INT NOT NULL,
    `session_type` VARCHAR(50) NOT NULL DEFAULT 'Ordinária',
    `session_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Agendada',
    `president_name` VARCHAR(150),
    `summary` TEXT,
    `minutes_text` LONGTEXT,
    `video_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sess_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sess_leg` FOREIGN KEY (`legislature_id`) REFERENCES `legislatures`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. SESSION AGENDA ITEMS (Pauta e Ordem do Dia)
CREATE TABLE IF NOT EXISTS `session_agenda_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` INT NOT NULL,
    `matter_id` INT NULL,
    `order_num` INT NOT NULL DEFAULT 1,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Pendente',
    `voting_type` VARCHAR(50) NOT NULL DEFAULT 'Nominal',
    `result` VARCHAR(50) DEFAULT 'Não votado',
    `votes_yes` INT DEFAULT 0,
    `votes_no` INT DEFAULT 0,
    `votes_abstention` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_agenda_session` FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_agenda_matter` FOREIGN KEY (`matter_id`) REFERENCES `legislative_matters`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. SESSION VOTES (Votos Nominais dos Vereadores)
CREATE TABLE IF NOT EXISTS `session_votes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `agenda_item_id` INT NOT NULL,
    `councilor_id` INT NOT NULL,
    `vote` VARCHAR(20) NOT NULL,
    `voted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_vote_item` FOREIGN KEY (`agenda_item_id`) REFERENCES `session_agenda_items`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_vote_counc` FOREIGN KEY (`councilor_id`) REFERENCES `councilors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. LEGAL NORMS (Leis e Normas Municipais)
CREATE TABLE IF NOT EXISTS `legal_norms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `norm_type` VARCHAR(80) NOT NULL,
    `number` INT NOT NULL,
    `year` INT NOT NULL,
    `promulgation_date` DATE NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `summary` TEXT NOT NULL,
    `full_text` LONGTEXT NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Vigente',
    `matter_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_norm_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_norm_matter` FOREIGN KEY (`matter_id`) REFERENCES `legislative_matters`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. EMPLOYEES (RH - Servidores e Colaboradores)
CREATE TABLE IF NOT EXISTS `employees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `cpf` VARCHAR(20) NOT NULL,
    `registration_number` VARCHAR(50) NOT NULL,
    `employment_type` VARCHAR(50) NOT NULL DEFAULT 'Efetivo',
    `job_title` VARCHAR(100) NOT NULL,
    `department` VARCHAR(100) NOT NULL,
    `salary` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `admission_date` DATE NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Ativo',
    `email` VARCHAR(150),
    `phone` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_emp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. LEAVES & VACATIONS (RH - Férias e Afastamentos)
CREATE TABLE IF NOT EXISTS `leaves_vacations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `leave_type` VARCHAR(50) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Aprovado',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_leave_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. TIME ATTENDANCES (RH - Ponto e Frequência)
CREATE TABLE IF NOT EXISTS `time_attendances` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `work_date` DATE NOT NULL,
    `clock_in` TIME NOT NULL,
    `lunch_out` TIME NULL,
    `lunch_in` TIME NULL,
    `clock_out` TIME NOT NULL,
    `total_hours` DECIMAL(4,2) DEFAULT 8.00,
    `status` VARCHAR(50) DEFAULT 'Normal',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_time_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. MATERIAL ITEMS (Almoxarifado - Itens de Catálogo)
CREATE TABLE IF NOT EXISTS `material_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `unit` VARCHAR(20) NOT NULL DEFAULT 'UN',
    `current_stock` INT NOT NULL DEFAULT 0,
    `minimum_stock` INT NOT NULL DEFAULT 10,
    `unit_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `location` VARCHAR(100) NOT NULL DEFAULT 'Almoxarifado Central',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mat_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. STOCK MOVEMENTS (Almoxarifado - Movimentações de Entrada e Saída)
CREATE TABLE IF NOT EXISTS `stock_movements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `material_item_id` INT NOT NULL,
    `movement_type` ENUM('ENTRADA', 'SAIDA', 'AJUSTE') NOT NULL,
    `quantity` INT NOT NULL,
    `previous_stock` INT NOT NULL,
    `new_stock` INT NOT NULL,
    `reason` VARCHAR(255) NOT NULL,
    `document_ref` VARCHAR(100),
    `user_name` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_smov_item` FOREIGN KEY (`material_item_id`) REFERENCES `material_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. STOCK REQUISITIONS (Almoxarifado - Requisições por Setor)
CREATE TABLE IF NOT EXISTS `stock_requisitions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `department` VARCHAR(100) NOT NULL,
    `requester_name` VARCHAR(150) NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Aguardando Aprovação',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_req_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. STOCK REQUISITION ITEMS (Itens da Requisição)
CREATE TABLE IF NOT EXISTS `stock_requisition_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `requisition_id` INT NOT NULL,
    `material_item_id` INT NOT NULL,
    `quantity_requested` INT NOT NULL,
    `quantity_delivered` INT DEFAULT 0,
    CONSTRAINT `fk_reqitem_req` FOREIGN KEY (`requisition_id`) REFERENCES `stock_requisitions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reqitem_mat` FOREIGN KEY (`material_item_id`) REFERENCES `material_items`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. SUPPLIERS (Compras - Cadastro de Fornecedores)
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `cnpj_cpf` VARCHAR(20) NOT NULL,
    `corporate_name` VARCHAR(200) NOT NULL,
    `trade_name` VARCHAR(200),
    `email` VARCHAR(150),
    `phone` VARCHAR(50),
    `city` VARCHAR(100),
    `state` VARCHAR(2),
    `status` VARCHAR(50) DEFAULT 'Ativo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sup_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. PROCUREMENTS (Licitações Lei 14.133/21)
CREATE TABLE IF NOT EXISTS `procurements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `process_number` VARCHAR(50) NOT NULL,
    `year` INT NOT NULL,
    `modality` VARCHAR(80) NOT NULL,
    `object_description` TEXT NOT NULL,
    `estimated_value` DECIMAL(12,2) NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Homologado',
    `opening_date` DATE NOT NULL,
    `pncp_published` TINYINT(1) DEFAULT 1,
    `pncp_link` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_proc_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. CONTRACTS (Contratos Administrativos)
CREATE TABLE IF NOT EXISTS `contracts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `procurement_id` INT NULL,
    `supplier_id` INT NOT NULL,
    `contract_number` VARCHAR(50) NOT NULL,
    `year` INT NOT NULL,
    `object_summary` TEXT NOT NULL,
    `total_value` DECIMAL(12,2) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `manager_name` VARCHAR(150) NOT NULL,
    `inspector_name` VARCHAR(150) NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Vigente',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_cont_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cont_proc` FOREIGN KEY (`procurement_id`) REFERENCES `procurements`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_cont_sup` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. ASSETS (Patrimônio e Bens Móveis)
CREATE TABLE IF NOT EXISTS `assets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `asset_tag` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `brand` VARCHAR(100),
    `serial_number` VARCHAR(100),
    `acquisition_date` DATE NOT NULL,
    `acquisition_value` DECIMAL(10,2) NOT NULL,
    `current_value` DECIMAL(10,2) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `responsible_name` VARCHAR(150) NOT NULL,
    `condition_status` VARCHAR(50) NOT NULL DEFAULT 'Bom',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_asset_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 25. VEHICLES (Frota Oficial)
CREATE TABLE IF NOT EXISTS `vehicles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `asset_id` INT NULL,
    `plate` VARCHAR(20) NOT NULL,
    `model` VARCHAR(100) NOT NULL,
    `year` INT NOT NULL,
    `fuel_type` VARCHAR(50) NOT NULL,
    `mileage` INT NOT NULL DEFAULT 0,
    `driver_name` VARCHAR(150) NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Disponível',
    `last_maintenance` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_veh_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_veh_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 26. BUDGET ALLOCATIONS (Orçamento e Dotações)
CREATE TABLE IF NOT EXISTS `budget_allocations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `initial_budget` DECIMAL(14,2) NOT NULL,
    `committed_budget` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `realized_budget` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `fiscal_year` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_budg_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 27. EXPENSE REQUESTS (Despesas e Diárias)
CREATE TABLE IF NOT EXISTS `expense_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `budget_allocation_id` INT NULL,
    `expense_type` VARCHAR(50) NOT NULL DEFAULT 'Geral',
    `beneficiary_name` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `request_date` DATE NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Paga',
    `payment_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_exp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exp_budg` FOREIGN KEY (`budget_allocation_id`) REFERENCES `budget_allocations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 28. PROTOCOLS (Protocolo Geral de Documentos)
CREATE TABLE IF NOT EXISTS `protocols` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `protocol_number` VARCHAR(50) NOT NULL,
    `year` INT NOT NULL,
    `sender_name` VARCHAR(150) NOT NULL,
    `sender_type` VARCHAR(50) NOT NULL DEFAULT 'Cidadão',
    `subject` VARCHAR(255) NOT NULL,
    `summary` TEXT NOT NULL,
    `destination_department` VARCHAR(100) NOT NULL,
    `urgency` VARCHAR(50) NOT NULL DEFAULT 'Normal',
    `status` VARCHAR(50) NOT NULL DEFAULT 'Aberto',
    `verification_hash` VARCHAR(64) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_prot_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 29. PROTOCOL DISPATCHES (Despachos de Protocolo)
CREATE TABLE IF NOT EXISTS `protocol_dispatches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `protocol_id` INT NOT NULL,
    `dispatch_text` TEXT NOT NULL,
    `user_name` VARCHAR(150) NOT NULL,
    `to_department` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pdisp_prot` FOREIGN KEY (`protocol_id`) REFERENCES `protocols`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 30. CITIZEN REQUESTS (Ouvidoria e SIC)
CREATE TABLE IF NOT EXISTS `citizen_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `protocol_code` VARCHAR(50) NOT NULL,
    `request_type` VARCHAR(50) NOT NULL,
    `citizen_name` VARCHAR(150),
    `citizen_email` VARCHAR(150),
    `citizen_phone` VARCHAR(50),
    `is_anonymous` TINYINT(1) DEFAULT 0,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Recebido',
    `reply_text` TEXT,
    `replied_at` DATETIME,
    `replied_by` VARCHAR(150),
    `deadline_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_citreq_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 31. INTERNAL CONTROLS (Controle Interno e Matriz de Riscos)
CREATE TABLE IF NOT EXISTS `internal_controls` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `risk_level` VARCHAR(50) NOT NULL DEFAULT 'Médio',
    `finding_description` TEXT NOT NULL,
    `recommendation` TEXT NOT NULL,
    `action_plan` TEXT NOT NULL,
    `responsible_name` VARCHAR(150) NOT NULL,
    `deadline` DATE NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Em Andamento',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ic_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 32. NEWS (Comunicação e Notícias)
CREATE TABLE IF NOT EXISTS `news` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(255),
    `content` LONGTEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT 'Institucional',
    `author_name` VARCHAR(150) NOT NULL,
    `image_url` VARCHAR(255),
    `published_at` DATETIME NOT NULL,
    `is_published` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_news_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 33. AI CONVERSATIONS (Logs e Rastreabilidade de IA)
CREATE TABLE IF NOT EXISTS `ai_conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `user_id` INT NULL,
    `module_context` VARCHAR(50) NOT NULL,
    `prompt` TEXT NOT NULL,
    `response` LONGTEXT NOT NULL,
    `sources_cited` TEXT,
    `latency_ms` INT DEFAULT 0,
    `user_feedback` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ai_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 34. AUDIT LOGS (Trilha de Auditoria Global)
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `user_id` INT NULL,
    `user_name` VARCHAR(150) NOT NULL DEFAULT 'Sistema',
    `action` VARCHAR(100) NOT NULL,
    `entity` VARCHAR(100) NOT NULL,
    `entity_id` INT NULL,
    `details` TEXT,
    `ip_address` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
