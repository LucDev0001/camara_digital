-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: camara_digital_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ai_conversations`
--

DROP TABLE IF EXISTS `ai_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `module_context` varchar(50) NOT NULL,
  `prompt` text NOT NULL,
  `response` longtext NOT NULL,
  `sources_cited` text DEFAULT NULL,
  `latency_ms` int(11) DEFAULT 0,
  `user_feedback` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ai_tenant` (`tenant_id`),
  CONSTRAINT `fk_ai_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_conversations`
--

LOCK TABLES `ai_conversations` WRITE;
/*!40000 ALTER TABLE `ai_conversations` DISABLE KEYS */;
INSERT INTO `ai_conversations` VALUES (1,1,1,'Legislativo','Quais leis municipais tratam sobre sustentabilidade e incentivo ao meio ambiente?','No acervo municipal foram localizadas duas normas principais relacionadas:\n1. **Lei Complementar nº 412/2025** (Código de Edificações Sustentáveis) - Cria o IPTU Verde com desconto de até 15% para captação solar e reúso de água.\n2. **Projeto de Lei nº 104/2026** (em tramitação) - Institui a obrigatoriedade de energia solar nos prédios públicos municipais.','Lei Complementar nº 412/2025; Projeto de Lei nº 104/2026',420,'positivo','2026-10-27 00:49:20'),(2,1,2,'Redação','Gerar justificativa fundamentada para Projeto de Lei sobre calçadas acessíveis.','A justificativa apoia-se no princípio constitucional da dignidade da pessoa humana e nas diretrizes da Lei Federal de Acessibilidade (Lei 10.098/2000). A padronização dos passeios públicos garante o direito de ir e vir a pessoas idosas e com deficiência, prevenindo acidentes urbanos.','Lei Federal nº 10.098/2000; NBR 9050',380,'positivo','2026-10-27 00:49:20'),(3,1,1,'Legislativo','energia solar','Com base no acervo documental e legislativo oficial do município, localizei as seguintes referências pertinentes à sua pesquisa:\n\n**1. Lei Complementar nº 412/2025** — *Código Municipal de Edificações Sustentáveis*\n• **Ementa:** Institui incentivos fiscais (IPTU Verde) para empreendimentos com captação de água de chuva e energia solar.\n• **Dispositivos aplicáveis:** Art. 1º Fica criado o Programa IPTU Verde, concedendo desconto de até 15% no imposto predial para imóveis dotados de tecnologias sustentáveis comprovadas pelo órgão ambiental....\n\n**• Proposição PL-104/2026 (Projeto de Lei Ordinária)**: *Programa Municipal de Energia Solar nos Prédios Públicos* (Status: **Em Tramitação**).\n  Dispõe sobre a obrigatoriedade gradual de instalação de placas fotovoltaicas e sistemas de captação de energia solar em prédios públicos municipais.\n\n\n> *Nota de Conformidade:* Esta resposta foi gerada via busca semântica em base de dados oficial auditada. A interpretação jurídica final e atos normativos cabem privativamente à Mesa Diretora e ao Plenário da Câmara.','Lei Complementar nº 412/2025 - Código Municipal de Edificações Sustentáveis; PL-104/2026 (Projeto de Lei Ordinária) - Situação: Em Tramitação',5,NULL,'2026-10-27 00:49:52'),(4,1,1,'Legislativo','Quais leis municipais tratam sobre meio ambiente e energia solar?','Com base no acervo documental e legislativo oficial do município, localizei as seguintes referências pertinentes à sua pesquisa:\n\n**1. Lei Complementar nº 412/2025** — *Código Municipal de Edificações Sustentáveis*\n• **Ementa:** Institui incentivos fiscais (IPTU Verde) para empreendimentos com captação de água de chuva e energia solar.\n• **Dispositivos aplicáveis:** Art. 1º Fica criado o Programa IPTU Verde, concedendo desconto de até 15% no imposto predial para imóveis dotados de tecnologias sustentáveis comprovadas pelo órgão ambiental....\n\n**• Proposição PL-104/2026 (Projeto de Lei Ordinária)**: *Programa Municipal de Energia Solar nos Prédios Públicos* (Status: **Em Tramitação**).\n  Dispõe sobre a obrigatoriedade gradual de instalação de placas fotovoltaicas e sistemas de captação de energia solar em prédios públicos municipais.\n\n**• Proposição REQ-089/2026 (Requerimento)**: *Informações sobre Cronograma de Obras nas UBSs Municipais* (Status: **Aprovado**).\n  Requer envio de relatório pormenorizado contendo o cronograma das obras de ampliação e reforma das 12 Unidades Básicas de Saúde da região sul.\n\n\n> *Nota de Conformidade:* Esta resposta foi gerada via busca semântica em base de dados oficial auditada. A interpretação jurídica final e atos normativos cabem privativamente à Mesa Diretora e ao Plenário da Câmara.','Lei Complementar nº 412/2025 - Código Municipal de Edificações Sustentáveis; PL-104/2026 (Projeto de Lei Ordinária) - Situação: Em Tramitação; REQ-089/2026 (Requerimento) - Situação: Aprovado',6,NULL,'2026-10-27 00:51:09');
/*!40000 ALTER TABLE `ai_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `asset_tag` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `acquisition_value` decimal(10,2) NOT NULL,
  `current_value` decimal(10,2) NOT NULL,
  `location` varchar(150) NOT NULL,
  `responsible_name` varchar(150) NOT NULL,
  `condition_status` varchar(50) NOT NULL DEFAULT 'Bom',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_asset_tenant` (`tenant_id`),
  CONSTRAINT `fk_asset_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (1,1,'PAT-2026-001','Servidor Rack Dell PowerEdge R650 64GB Xeon','Informática e Servidores','Dell','SERV-DELL-9842A','2026-03-05',38500.00,36000.00,'Data Center / CPD','Dr. Luciano Albuquerque','Novo','2026-10-27 00:49:20'),(2,1,'PAT-2025-045','Mesa de Som Digital 24 Canais Plenário','Equipamentos Audiovisuais','Yamaha','YAM-TF3-4819','2025-04-12',19800.00,16500.00,'Cabine de Som do Plenário','Juliana Rios','Bom','2026-10-27 00:49:20'),(3,1,'PAT-2024-112','Mesa Diretora em Madeira Nobre com 5 Postos','Móveis e Utensílios','Marcenaria Central','MOB-PLEN-001','2024-01-10',14200.00,11000.00,'Plenário Principal','Carlos Eduardo Silva','Bom','2026-10-27 00:49:20'),(4,1,'PAT-2025-089','Veículo Oficial Sedan Toyota Corolla 2.0 Flex','Veículos e Frota','Toyota','CHAS-9BR-2025-99','2025-08-20',148000.00,135000.00,'Garagem Oficial','Sérgio Ramos (Motorista)','Bom','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(150) NOT NULL DEFAULT 'Sistema',
  `action` varchar(100) NOT NULL,
  `entity` varchar(100) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_audit_tenant` (`tenant_id`),
  CONSTRAINT `fk_audit_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,1,'Dr. Luciano Albuquerque','LOGIN','users',1,'Login efetuado com sucesso via credencial administrativa.','127.0.0.1','2026-10-27 00:49:20'),(2,1,2,'Ver. Carlos Eduardo Silva','CRIAR_PROPOSICAO','legislative_matters',1,'Apresentação formal do Projeto de Lei nº 104/2026 no sistema.','127.0.0.1','2026-10-27 00:49:20'),(3,1,4,'Fernanda Rezende','PUBLICAR_SESSAO','sessions',1,'Publicação oficial da ata e resultado de votação da 42ª Sessão Ordinária.','127.0.0.1','2026-10-27 00:49:20'),(4,1,1,'Dr. Luciano Albuquerque','LOGIN_QUICK','users',1,'Login rápido executado como: Dr. Luciano Albuquerque','::1','2026-10-27 00:49:52'),(5,1,1,'Dr. Luciano Albuquerque','IA_QUERY','ai_conversations',3,'Consulta IA em Legislativo: energia solar','::1','2026-10-27 00:49:52'),(6,1,1,'Dr. Luciano Albuquerque','VOTO_PLENARIO','session_agenda_items',3,'Voto [Sim] registrado para o vereador ID: 2','::1','2026-10-27 00:50:48'),(7,1,1,'Dr. Luciano Albuquerque','VOTO_PLENARIO','session_agenda_items',3,'Voto [Não] registrado para o vereador ID: 1','::1','2026-10-27 00:50:50'),(8,1,1,'Dr. Luciano Albuquerque','VOTO_PLENARIO','session_agenda_items',3,'Voto [Abstenção] registrado para o vereador ID: 3','::1','2026-10-27 00:50:51'),(9,1,1,'Dr. Luciano Albuquerque','VOTO_PLENARIO','session_agenda_items',3,'Voto [Não] registrado para o vereador ID: 4','::1','2026-10-27 00:50:53'),(10,1,1,'Dr. Luciano Albuquerque','IA_QUERY','ai_conversations',4,'Consulta IA em Legislativo: Quais leis municipais tratam sobre meio ambiente e energia solar?','::1','2026-10-27 00:51:09');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budget_allocations`
--

DROP TABLE IF EXISTS `budget_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budget_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `initial_budget` decimal(14,2) NOT NULL,
  `committed_budget` decimal(14,2) NOT NULL DEFAULT 0.00,
  `realized_budget` decimal(14,2) NOT NULL DEFAULT 0.00,
  `fiscal_year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_budg_tenant` (`tenant_id`),
  CONSTRAINT `fk_budg_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budget_allocations`
--

LOCK TABLES `budget_allocations` WRITE;
/*!40000 ALTER TABLE `budget_allocations` DISABLE KEYS */;
INSERT INTO `budget_allocations` VALUES (1,1,'01.031.0001.2001','Manutenção Geral e Operacional do Poder Legislativo',12500000.00,4200000.00,3100000.00,2026,'2026-10-27 00:49:20'),(2,1,'01.031.0001.2002','Capacitação e Desenvolvimento de Servidores e Parlamentares',350000.00,85000.00,62000.00,2026,'2026-10-27 00:49:20'),(3,1,'01.031.0001.2003','Modernização Tecnológica e Transformação Digital',980000.00,320000.00,240000.00,2026,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `budget_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citizen_requests`
--

DROP TABLE IF EXISTS `citizen_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citizen_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `protocol_code` varchar(50) NOT NULL,
  `request_type` varchar(50) NOT NULL,
  `citizen_name` varchar(150) DEFAULT NULL,
  `citizen_email` varchar(150) DEFAULT NULL,
  `citizen_phone` varchar(50) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Recebido',
  `reply_text` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` varchar(150) DEFAULT NULL,
  `deadline_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_citreq_tenant` (`tenant_id`),
  CONSTRAINT `fk_citreq_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citizen_requests`
--

LOCK TABLES `citizen_requests` WRITE;
/*!40000 ALTER TABLE `citizen_requests` DISABLE KEYS */;
INSERT INTO `citizen_requests` VALUES (1,1,'OUV-2026-10492','SIC - Pedido LAI','Mateus Cavalcanti Neves','mateus.neves@gmail.com','(67) 99123-4567',0,'Detalhamento dos Gastos com Diárias do 1º Trimestre de 2026','Solicito a planilha detalhada contendo nome dos parlamentares e servidores que receberam diárias no período de janeiro a março de 2026, com destino e justificativa.','Respondido','Prezado cidadão, informamos que os dados solicitados já se encontram integralmente publicados no Portal da Transparência da Câmara, na aba \"Diárias e Despesas\". Disponibilizamos em anexo a planilha consolidada em formato aberto CSV.','2026-04-12 11:20:00','Aline Ferreira (Ouvidora)','2026-04-20','2026-10-27 00:49:20'),(2,1,'OUV-2026-10515','Reclamação','Cidadão Anônimo',NULL,NULL,1,'Demora no início da transmissão ao vivo da última sessão','Gostaria de registrar que a transmissão no canal do YouTube começou com 25 minutos de atraso na última terça-feira, perdendo a leitura da ata.','Recebido',NULL,NULL,NULL,'2026-05-05','2026-10-27 00:49:20'),(3,1,'OUV-2026-10530','Sugestão','Helena Beatriz Martins','helena.martins@outlook.com','(67) 99234-5678',0,'Criação do Programa Câmara Jovem para estudantes de escolas públicas','Sugiro que a Câmara promova uma edição anual da Câmara Jovem para incentivar a cidadania e o debate democrático entre estudantes da nossa cidade.','Em Tramitação',NULL,NULL,NULL,'2026-05-10','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `citizen_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committees`
--

DROP TABLE IF EXISTS `committees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `committees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `president_name` varchar(150) DEFAULT NULL,
  `rapporteur_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_com_tenant` (`tenant_id`),
  CONSTRAINT `fk_com_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `committees`
--

LOCK TABLES `committees` WRITE;
/*!40000 ALTER TABLE `committees` DISABLE KEYS */;
INSERT INTO `committees` VALUES (1,1,'Comissão de Constituição, Justiça e Redação Final (CCJ)','Avalia a legalidade, constitucionalidade e técnica legislativa de todas as matérias.','Dra. Beatriz Toledo','Carlos Eduardo Silva','2026-10-27 00:49:20'),(2,1,'Comissão de Finanças, Orçamento e Fiscalização Financeira','Aprecia o plano plurianual, diretrizes orçamentárias e contas públicas.','Rodrigo Mendes de Castro','Mariana Vasconcelos','2026-10-27 00:49:20'),(3,1,'Comissão de Educação, Saúde, Meio Ambiente e Cidadania','Foco no acompanhamento de políticas públicas sociais e defesa ambiental.','Prof. Lucas Brandão','Dra. Beatriz Toledo','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `committees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `procurement_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `contract_number` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `object_summary` text NOT NULL,
  `total_value` decimal(12,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `manager_name` varchar(150) NOT NULL,
  `inspector_name` varchar(150) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Vigente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_cont_tenant` (`tenant_id`),
  KEY `fk_cont_proc` (`procurement_id`),
  KEY `fk_cont_sup` (`supplier_id`),
  CONSTRAINT `fk_cont_proc` FOREIGN KEY (`procurement_id`) REFERENCES `procurements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cont_sup` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `fk_cont_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
INSERT INTO `contracts` VALUES (1,1,1,2,'CONT-008',2026,'Fornecimento e manutenção preventiva dos equipamentos de infraestrutura de rede e servidores de dados.',139800.00,'2026-03-01','2027-02-28','Dr. Luciano Albuquerque','Lucas Pires Camargo','Vigente','2026-10-27 00:49:20'),(2,1,2,1,'CONT-011',2026,'Fornecimento parcelado de papéis, toners e materiais de escritório para a Câmara Municipal.',46500.00,'2026-02-01','2027-01-31','Cláudio Arruda','Roberto Guimarães','Vigente','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `councilors`
--

DROP TABLE IF EXISTS `councilors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `councilors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `legislature_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `political_party` varchar(50) NOT NULL,
  `board_role` varchar(50) DEFAULT 'Membro',
  `photo_url` varchar(255) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_counc_tenant` (`tenant_id`),
  KEY `fk_counc_leg` (`legislature_id`),
  CONSTRAINT `fk_counc_leg` FOREIGN KEY (`legislature_id`) REFERENCES `legislatures` (`id`),
  CONSTRAINT `fk_counc_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `councilors`
--

LOCK TABLES `councilors` WRITE;
/*!40000 ALTER TABLE `councilors` DISABLE KEYS */;
INSERT INTO `councilors` VALUES (1,1,2,1,'Carlos Eduardo Silva','União Brasil','Presidente','https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&auto=format&fit=crop&q=80','Advogado, 3º mandato parlamentar, atuante nas áreas de transparência e infraestrutura.','carlos.silva@camara.ms.gov.br','(67) 99981-1001',1,'2026-10-27 00:49:20'),(2,1,3,1,'Mariana Vasconcelos','PSD','Vice-Presidente','https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80','Professora universitária, defensora dos direitos da infância, educação inclusiva e saúde preventiva.','mariana.vasconcelos@camara.ms.gov.br','(67) 99982-2002',1,'2026-10-27 00:49:20'),(3,1,NULL,1,'Rodrigo Mendes de Castro','MDB','1º Secretário','https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80','Empresário e economista, focado na atração de investimentos e simplificação tributária.','rodrigo.mendes@camara.ms.gov.br','(67) 99983-3003',1,'2026-10-27 00:49:20'),(4,1,NULL,1,'Dra. Beatriz Toledo','PSDB','2º Secretária','https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80','Médica cardiologista com atuação comunitária e programas de saúde da família.','beatriz.toledo@camara.ms.gov.br','(67) 99984-4004',1,'2026-10-27 00:49:20'),(5,1,NULL,1,'Prof. Lucas Brandão','PP','Membro','https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80','Biólogo e professor de ensino médio, pautas voltadas à sustentabilidade e causa animal.','lucas.brandao@camara.ms.gov.br','(67) 99985-5005',1,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `councilors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `manager_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_dept_tenant` (`tenant_id`),
  CONSTRAINT `fk_dept_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,1,'Presidência e Mesa Diretora','PRES','Ver. Carlos Eduardo Silva','presidencia@camara.ms.gov.br','2026-10-27 00:49:20'),(2,1,'Secretaria Legislativa','SECLEG','Fernanda Rezende','secretaria@camara.ms.gov.br','2026-10-27 00:49:20'),(3,1,'Diretoria Administrativa','DIRADM','Cláudio Arruda','adm@camara.ms.gov.br','2026-10-27 00:49:20'),(4,1,'Divisão de Recursos Humanos','DRH','Marcelo Nogueira','rh@camara.ms.gov.br','2026-10-27 00:49:20'),(5,1,'Almoxarifado e Patrimônio','ALMOX','Roberto Guimarães','almoxarifado@camara.ms.gov.br','2026-10-27 00:49:20'),(6,1,'Divisão de Licitações e Contratos','COMPRAS','Patrícia Prado','licitacoes@camara.ms.gov.br','2026-10-27 00:49:20'),(7,1,'Diretoria Financeira e Contábil','FIN','Gilberto Fontes','financeiro@camara.ms.gov.br','2026-10-27 00:49:20'),(8,1,'Unidade de Controle Interno','UCI','Dra. Vanessa Meireles','controle@camara.ms.gov.br','2026-10-27 00:49:20'),(9,1,'Ouvidoria e SIC','OUV','Aline Ferreira','ouvidoria@camara.ms.gov.br','2026-10-27 00:49:20'),(10,1,'Assessoria de Comunicação','ASCOM','Juliana Rios','comunicacao@camara.ms.gov.br','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `registration_number` varchar(50) NOT NULL,
  `employment_type` varchar(50) NOT NULL DEFAULT 'Efetivo',
  `job_title` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `admission_date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Ativo',
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_emp_tenant` (`tenant_id`),
  CONSTRAINT `fk_emp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,1,'Juliana de Oliveira Rios','234.987.123-45','MAT-1042','Efetivo','Analista Legislativo de Comunicação','Assessoria de Comunicação',7850.00,'2021-03-01','Ativo','juliana.rios@camara.ms.gov.br','(67) 99811-7788','2026-10-27 00:49:20'),(2,1,'Renato Sanches Barbosa','345.876.234-56','MAT-1088','Comissionado','Chefe de Gabinete Parlamentar','Gabinete Parlamentar 01',9200.00,'2025-01-05','Ativo','renato.sanches@camara.ms.gov.br','(67) 99822-8899','2026-10-27 00:49:20'),(3,1,'Lucas Pires Camargo','456.765.345-67','MAT-1105','Efetivo','Técnico em Informática','Tecnologia da Informação',5400.00,'2022-08-15','Ativo','lucas.pires@camara.ms.gov.br','(67) 99833-9900','2026-10-27 00:49:20'),(4,1,'Carla Mendonça Vieira','567.654.456-78','MAT-1011','Efetivo','Contadora Pública','Diretoria Financeira',11400.00,'2019-02-10','Ativo','carla.mendonca@camara.ms.gov.br','(67) 99844-0011','2026-10-27 00:49:20'),(5,1,'Thiago Henrique Siqueira','678.543.567-89','MAT-1220','Estagiário','Estagiário de Direito','Secretaria Legislativa',1600.00,'2026-02-01','Ativo','thiago.estagio@camara.ms.gov.br','(67) 99855-1122','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_requests`
--

DROP TABLE IF EXISTS `expense_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `budget_allocation_id` int(11) DEFAULT NULL,
  `expense_type` varchar(50) NOT NULL DEFAULT 'Geral',
  `beneficiary_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `request_date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Paga',
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_exp_tenant` (`tenant_id`),
  KEY `fk_exp_budg` (`budget_allocation_id`),
  CONSTRAINT `fk_exp_budg` FOREIGN KEY (`budget_allocation_id`) REFERENCES `budget_allocations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_exp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_requests`
--

LOCK TABLES `expense_requests` WRITE;
/*!40000 ALTER TABLE `expense_requests` DISABLE KEYS */;
INSERT INTO `expense_requests` VALUES (1,1,2,'Diária de Viagem','Carlos Eduardo Silva','Participação no Encontro Nacional de Presidentes de Câmaras Municipais em Brasília-DF.',2800.00,'2026-03-10','Paga','2026-03-14','2026-10-27 00:49:20'),(2,1,3,'Serviço de Terceiros TI','TechMS Soluções Ltda','Pagamento da medição 01 do contrato de infraestrutura de TI.',11650.00,'2026-04-01','Paga','2026-04-05','2026-10-27 00:49:20'),(3,1,1,'Consumo de Combustível','Auto Posto Alvorada','Abastecimento mensal da frota oficial de veículos da Câmara.',4320.50,'2026-04-10','Autorizada',NULL,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `expense_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_controls`
--

DROP TABLE IF EXISTS `internal_controls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_controls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `risk_level` varchar(50) NOT NULL DEFAULT 'Médio',
  `finding_description` text NOT NULL,
  `recommendation` text NOT NULL,
  `action_plan` text NOT NULL,
  `responsible_name` varchar(150) NOT NULL,
  `deadline` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Em Andamento',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ic_tenant` (`tenant_id`),
  CONSTRAINT `fk_ic_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_controls`
--

LOCK TABLES `internal_controls` WRITE;
/*!40000 ALTER TABLE `internal_controls` DISABLE KEYS */;
INSERT INTO `internal_controls` VALUES (1,1,'Aderência às Publicações Obrigatórias no PNCP (Lei 14.133)','Legal/Regulamentar','Alto','Identificada necessidade de automação de sincronização dos avisos de dispensa com a API nacional do PNCP.','Adotar conector oficial e checklist obrigatório antes da emissão do termo de homologação.','Implementar trava de sistema que exige código de publicação PNCP para emissão da nota de empenho.','Patrícia Prado (Compras)','2026-05-30','Em Andamento','2026-10-27 00:49:20'),(2,1,'Segurança e Isolamento de Dados Pessoais de Ouvidoria (LGPD)','TI / Privacidade','Médio','Verificar se denúncias com solicitação de sigilo ocultam os dados de contato em relatórios públicos.','Garantir segregação técnica de manifestações anônimas ou sigilosas no banco de dados e APIs.','Concluída a revisão de permissões dos perfis de atendimento com aprovação da DPO.','Dra. Vanessa Meireles (Controle)','2026-04-15','Concluído','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `internal_controls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves_vacations`
--

DROP TABLE IF EXISTS `leaves_vacations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves_vacations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Aprovado',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_leave_emp` (`employee_id`),
  CONSTRAINT `fk_leave_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves_vacations`
--

LOCK TABLES `leaves_vacations` WRITE;
/*!40000 ALTER TABLE `leaves_vacations` DISABLE KEYS */;
INSERT INTO `leaves_vacations` VALUES (1,1,'Férias Regulamentares','2026-07-01','2026-07-20','Aprovado','Período aquisitivo 2024/2025 deferido pelo RH.','2026-10-27 00:49:20'),(2,4,'Licença Maternidade','2026-05-10','2026-11-10','Aprovado','Afastamento legal de 180 dias conforme estatuto dos servidores municipais.','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `leaves_vacations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legal_norms`
--

DROP TABLE IF EXISTS `legal_norms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legal_norms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `norm_type` varchar(80) NOT NULL,
  `number` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `promulgation_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `full_text` longtext NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Vigente',
  `matter_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_norm_tenant` (`tenant_id`),
  KEY `fk_norm_matter` (`matter_id`),
  CONSTRAINT `fk_norm_matter` FOREIGN KEY (`matter_id`) REFERENCES `legislative_matters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_norm_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legal_norms`
--

LOCK TABLES `legal_norms` WRITE;
/*!40000 ALTER TABLE `legal_norms` DISABLE KEYS */;
INSERT INTO `legal_norms` VALUES (1,1,'Lei Ordinária',5890,2026,'2026-01-20','Lei Municipal da Mobilidade e Calçadas Acessíveis','Estabelece os parâmetros de acessibilidade, padronização e manutenção das calçadas e passeios públicos do município.','Art. 1º Todas as calçadas no perímetro urbano deverão observar as normas técnicas de acessibilidade universal NBR 9050. Art. 2º Fica vedada a instalação de obstáculos que impeçam a circulação de pedestres e cadeirantes.','Vigente',NULL,'2026-10-27 00:49:20'),(2,1,'Lei Complementar',412,2025,'2025-11-14','Código Municipal de Edificações Sustentáveis','Institui incentivos fiscais (IPTU Verde) para empreendimentos com captação de água de chuva e energia solar.','Art. 1º Fica criado o Programa IPTU Verde, concedendo desconto de até 15% no imposto predial para imóveis dotados de tecnologias sustentáveis comprovadas pelo órgão ambiental.','Vigente',NULL,'2026-10-27 00:49:20'),(3,1,'Lei Ordinária',5820,2025,'2025-06-30','Política Municipal de Transparência Ativa e Dados Abertos','Regulamenta o acesso irrestrito às informações orçamentárias, financeiras e contratos do município em formato aberto.','Art. 1º Os dados produzidos pela Administração Pública Municipal serão disponibilizados em formato legível por máquina (CSV, JSON, XML) no Portal da Transparência em até 48 horas.','Vigente',NULL,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `legal_norms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legislative_matters`
--

DROP TABLE IF EXISTS `legislative_matters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legislative_matters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `number` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `type` varchar(80) NOT NULL,
  `author_councilor_id` int(11) DEFAULT NULL,
  `author_name` varchar(150) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `full_text` longtext DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Em Tramitação',
  `urgency` varchar(50) NOT NULL DEFAULT 'Normal',
  `presentation_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_matters_tenant` (`tenant_id`),
  KEY `fk_matters_author` (`author_councilor_id`),
  CONSTRAINT `fk_matters_author` FOREIGN KEY (`author_councilor_id`) REFERENCES `councilors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_matters_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legislative_matters`
--

LOCK TABLES `legislative_matters` WRITE;
/*!40000 ALTER TABLE `legislative_matters` DISABLE KEYS */;
INSERT INTO `legislative_matters` VALUES (1,1,'PL-104/2026',104,2026,'Projeto de Lei Ordinária',1,'Carlos Eduardo Silva','Programa Municipal de Energia Solar nos Prédios Públicos','Dispõe sobre a obrigatoriedade gradual de instalação de placas fotovoltaicas e sistemas de captação de energia solar em prédios públicos municipais.','Art. 1º Fica instituído o Programa Municipal de Energia Solar Fotovoltaica na Administração Pública Direta e Indireta do Município. Art. 2º Novas edificações públicas municipais deverão conter previsão arquitetônica e estrutural para captação de energia limpa. Art. 3º Esta Lei entra em vigor na data de sua publicação.','Em Tramitação','Normal','2026-02-10','2026-10-27 00:49:20','2026-10-27 00:49:20'),(2,1,'PL-112/2026',112,2026,'Projeto de Lei Ordinária',2,'Mariana Vasconcelos','Programa Municipal de Apoio Integral à Primeira Infância','Institui a política municipal integrada para o desenvolvimento integral de crianças de zero a seis anos de idade no município.','Art. 1º Fica instituída a Política Municipal de Atenção Integral à Primeira Infância. Art. 2º O Poder Executivo garantirá prioritariamente vagas em creches públicas e acompanhamento pré-natal multiprofissional. Art. 3º Esta Lei entrará em vigor 60 dias após sua publicação oficial.','Aprovado','Urgência','2026-03-05','2026-10-27 00:49:20','2026-10-27 00:49:20'),(3,1,'PLC-015/2026',15,2026,'Projeto de Lei Complementar',1,'Carlos Eduardo Silva','Modernização do Processo Eletrônico Legislativo e Transparência','Altera dispositivos da Lei Orgânica Municipal para regulamentar a assinatura digital qualificada e a tramitação 100% eletrônica de proposições.','Art. 1º A tramitação de projetos de lei, vetos e requerimentos no âmbito do Poder Legislativo dar-se-á em suporte eletrônico digital. Art. 2º Os documentos digitais produzidos no sistema oficial possuem fé pública institucional.','Em Tramitação','Normal','2026-03-12','2026-10-27 00:49:20','2026-10-27 00:49:20'),(4,1,'IND-450/2026',450,2026,'Indicação',3,'Rodrigo Mendes de Castro','Recapeamento Asfáltico e Iluminação LED no Bairro Universitário','Indica ao Ilustríssimo Prefeito Municipal a urgente necessidade de pavimentação e substituição de luminárias de vapor por luminárias em LED nas vias principais.','O Vereador que a esta subscreve requer, na forma regimental, que seja encaminhado expediente ao Chefe do Poder Executivo, indicando a realização de serviços de recapeamento e iluminação pública no Bairro Universitário.','Aprovado','Normal','2026-03-20','2026-10-27 00:49:20','2026-10-27 00:49:20'),(5,1,'REQ-089/2026',89,2026,'Requerimento',4,'Dra. Beatriz Toledo','Informações sobre Cronograma de Obras nas UBSs Municipais','Requer envio de relatório pormenorizado contendo o cronograma das obras de ampliação e reforma das 12 Unidades Básicas de Saúde da região sul.','Requeiro à Mesa Diretora, ouvido o Plenário, que solicite à Secretaria Municipal de Saúde o envio das planilhas de execução orçamentária e cronograma físico das obras em andamento.','Aprovado','Normal','2026-04-02','2026-10-27 00:49:20','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `legislative_matters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legislatures`
--

DROP TABLE IF EXISTS `legislatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legislatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `start_year` int(11) NOT NULL,
  `end_year` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_leg_tenant` (`tenant_id`),
  CONSTRAINT `fk_leg_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legislatures`
--

LOCK TABLES `legislatures` WRITE;
/*!40000 ALTER TABLE `legislatures` DISABLE KEYS */;
INSERT INTO `legislatures` VALUES (1,1,11,2025,2028,1,'2026-10-27 00:49:20'),(2,2,10,2025,2028,1,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `legislatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_items`
--

DROP TABLE IF EXISTS `material_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL DEFAULT 'UN',
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `minimum_stock` int(11) NOT NULL DEFAULT 10,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `location` varchar(100) NOT NULL DEFAULT 'Almoxarifado Central',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_mat_tenant` (`tenant_id`),
  CONSTRAINT `fk_mat_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_items`
--

LOCK TABLES `material_items` WRITE;
/*!40000 ALTER TABLE `material_items` DISABLE KEYS */;
INSERT INTO `material_items` VALUES (1,1,'MAT-001','Papel Sulfite A4 75g (Caixa com 10 resmas)','Material de Escritório','CX',45,15,248.50,'Prateleira A1','2026-10-27 00:49:20'),(2,1,'MAT-002','Cartucho de Toner HP LaserJet CF258A','Informática e Impressão','UN',8,4,385.00,'Armário Blindado B2','2026-10-27 00:49:20'),(3,1,'MAT-003','Caneta Esferográfica Azul 1.0mm (Caixa 50 un)','Material de Escritório','CX',25,10,42.90,'Prateleira A2','2026-10-27 00:49:20'),(4,1,'MAT-004','Café Torrado e Moído Tradicional 500g','Copa e Cozinha','PCT',32,20,22.80,'Despensa Central','2026-10-27 00:49:20'),(5,1,'MAT-005','Pasta Suspensa Kraft Completa com Visor (Pct 50)','Arquivo e Protocolo','PCT',14,8,115.00,'Prateleira C3','2026-10-27 00:49:20'),(6,1,'MAT-006','Água Mineral sem Gás 500ml (Fardo c/ 12)','Copa e Cozinha','FD',60,25,18.00,'Despensa Central','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `material_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matter_movements`
--

DROP TABLE IF EXISTS `matter_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matter_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matter_id` int(11) NOT NULL,
  `from_dept` varchar(100) DEFAULT NULL,
  `to_dept` varchar(100) NOT NULL,
  `dispatch_text` text NOT NULL,
  `status_after` varchar(50) NOT NULL,
  `movement_date` datetime NOT NULL,
  `user_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_move_matter` (`matter_id`),
  CONSTRAINT `fk_move_matter` FOREIGN KEY (`matter_id`) REFERENCES `legislative_matters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matter_movements`
--

LOCK TABLES `matter_movements` WRITE;
/*!40000 ALTER TABLE `matter_movements` DISABLE KEYS */;
INSERT INTO `matter_movements` VALUES (1,1,'Gabinete Parlamentar 01','Secretaria Legislativa','Protocolo inicial do Projeto de Lei nº 104/2026 para leitura em sessão plenária.','Em Tramitação','2026-02-10 14:30:00','Carlos Eduardo Silva','2026-10-27 00:49:20'),(2,1,'Secretaria Legislativa','Comissão de Constituição e Justiça','Encaminhado à CCJ para elaboração de parecer de admissibilidade e redação final.','Em Análise na CCJ','2026-02-12 10:00:00','Fernanda Rezende','2026-10-27 00:49:20'),(3,2,'Comissão de Constituição e Justiça','Plenário','Parecer favorável aprovado por unanimidade. Encaminhado para inclusão na Ordem do Dia.','Pronto para Votação','2026-03-18 16:45:00','Dra. Beatriz Toledo','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `matter_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Institucional',
  `author_name` varchar(150) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `published_at` datetime NOT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_news_tenant` (`tenant_id`),
  CONSTRAINT `fk_news_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,1,'Câmara aprova por unanimidade projeto de apoio à Primeira Infância','camara-aprova-projeto-primeira-infancia','Proposição garante ampliação de vagas em creches e acompanhamento integrado para crianças.','Em sessão plenária histórica realizada nesta quarta-feira, os vereadores aprovaram por unanimidade o Projeto de Lei nº 112/2026. A proposta institui diretrizes para a política municipal da primeira infância, unindo esforços das secretarias de educação e saúde.','Legislativo','Assessoria de Comunicação','https://images.unsplash.com/photo-1577495508048-b635879837f1?w=600&auto=format&fit=crop&q=80','2026-04-16 10:00:00',1,'2026-10-27 00:49:20'),(2,1,'Portal da Transparência atinge nota máxima em avaliação dos órgãos de controle','portal-transparencia-nota-maxima','Avaliação independente reconheceu a facilidade de acesso a dados em formato aberto e prazos de resposta da Ouvidoria.','A modernização digital da Câmara Municipal resultou na obtenção do Selo Diamante de Transparência Pública. O cidadão agora tem acesso imediato a todas as contas, contratos e leis aprovadas com apenas um clique.','Institucional','Assessoria de Comunicação','https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&auto=format&fit=crop&q=80','2026-04-20 14:30:00',1,'2026-10-27 00:49:20'),(3,1,'Audiência Pública debaterá modernização da iluminação e energia solar no município','audiencia-publica-energia-solar','Encontro aberto à população será realizado no Plenário na próxima quinta-feira.','A Presidência da Câmara convida toda a comunidade, empresários e especialistas do setor energético para debater o Projeto de Lei nº 104/2026, que incentiva a instalação de usinas solares em prédios públicos.','Sessões','Assessoria de Comunicação','https://images.unsplash.com/photo-1508873696983-2df5293cb32f?w=600&auto=format&fit=crop&q=80','2026-04-22 09:00:00',1,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procurements`
--

DROP TABLE IF EXISTS `procurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `procurements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `process_number` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `modality` varchar(80) NOT NULL,
  `object_description` text NOT NULL,
  `estimated_value` decimal(12,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Homologado',
  `opening_date` date NOT NULL,
  `pncp_published` tinyint(1) DEFAULT 1,
  `pncp_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_proc_tenant` (`tenant_id`),
  CONSTRAINT `fk_proc_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procurements`
--

LOCK TABLES `procurements` WRITE;
/*!40000 ALTER TABLE `procurements` DISABLE KEYS */;
INSERT INTO `procurements` VALUES (1,1,'PE-004/2026',2026,'Pregão Eletrônico (Lei 14.133)','Contratação de empresa especializada em fornecimento de materiais permanentes de TI e servidores.',145000.00,'Homologado','2026-02-15',1,'https://pncp.gov.br/app/editais/03243511000180/2026/000004','2026-10-27 00:49:20'),(2,1,'DL-012/2026',2026,'Dispensa de Licitação (Art. 75, II)','Aquisição contínua de suprimentos de escritório e papelaria para consumo anual dos gabinetes.',48900.00,'Homologado','2026-01-28',1,'https://pncp.gov.br/app/editais/03243511000180/2026/000012','2026-10-27 00:49:20'),(3,1,'PE-008/2026',2026,'Pregão Eletrônico (Lei 14.133)','Prestação de serviços contínuos de transmissão e gravação audiovisual das sessões plenárias.',88000.00,'Em Andamento','2026-05-02',1,'https://pncp.gov.br/app/editais/03243511000180/2026/000008','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `procurements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `protocol_dispatches`
--

DROP TABLE IF EXISTS `protocol_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `protocol_dispatches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `protocol_id` int(11) NOT NULL,
  `dispatch_text` text NOT NULL,
  `user_name` varchar(150) NOT NULL,
  `to_department` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pdisp_prot` (`protocol_id`),
  CONSTRAINT `fk_pdisp_prot` FOREIGN KEY (`protocol_id`) REFERENCES `protocols` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `protocol_dispatches`
--

LOCK TABLES `protocol_dispatches` WRITE;
/*!40000 ALTER TABLE `protocol_dispatches` DISABLE KEYS */;
INSERT INTO `protocol_dispatches` VALUES (1,1,'Recebido e cadastrado no protocolo geral. Encaminhado à Presidência para análise e despacho.','Aline Ferreira','Presidência e Mesa Diretora','2026-10-27 00:49:20'),(2,1,'Ciente. Encaminhado à Secretaria Legislativa para transformar em Indicação parlamentar.','Carlos Eduardo Silva','Secretaria Legislativa','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `protocol_dispatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `protocols`
--

DROP TABLE IF EXISTS `protocols`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `protocols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `protocol_number` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_type` varchar(50) NOT NULL DEFAULT 'Cidadão',
  `subject` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `destination_department` varchar(100) NOT NULL,
  `urgency` varchar(50) NOT NULL DEFAULT 'Normal',
  `status` varchar(50) NOT NULL DEFAULT 'Aberto',
  `verification_hash` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_prot_tenant` (`tenant_id`),
  CONSTRAINT `fk_prot_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `protocols`
--

LOCK TABLES `protocols` WRITE;
/*!40000 ALTER TABLE `protocols` DISABLE KEYS */;
INSERT INTO `protocols` VALUES (1,1,'PROT-2026/00142',2026,'Associação de Moradores do Bairro Pioneiros','Cidadão','Reivindicação de Implantação de Área de Lazer e Academia ao Ar Livre','Solicita apoio do Poder Legislativo junto à Prefeitura para urbanização de praça pública.','Gabinete da Presidência','Normal','Despachado','a3f5c78912e4b6c8912301fae87901234567890abcdef1234567890abcdef123','2026-10-27 00:49:20'),(2,1,'PROT-2026/00155',2026,'Tribunal de Contas do Estado (TCE-MS)','Órgão Externo','Notificação de Remessa das Contas do Exercício Anterior','Encaminha ofício de confirmação de recebimento da prestação de contas digital da Câmara.','Unidade de Controle Interno','Alta','Em Análise','b8e9f0123456789abcdef1234567890abcdef1234567890abcdef1234567890a','2026-10-27 00:49:20'),(3,1,'PROT-2026/00168',2026,'Sindicato dos Servidores Municipais','Entidade de Classe','Proposta de Reajuste do Auxílio Alimentação para o Exercício 2026','Minuta de reivindicação salarial e benefícios funcionais para apreciação da Mesa Diretora.','Diretoria Administrativa','Normal','Aberto','c9a0b1234567890abcdef1234567890abcdef1234567890abcdef1234567890b','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `protocols` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador Geral','admin','Acesso total irrestrito a todos os módulos e configurações da Câmara',10),(2,'Presidente / Mesa Diretora','mesa','Gestão institucional de pautas, ordens do dia, promulgação e atos',8),(3,'Vereador','vereador','Apresentação de proposições, votação em plenário e gestão de gabinete',5),(4,'Secretaria Legislativa','secretaria','Controle da tramitação, atas, comissões e redação final',6),(5,'Recursos Humanos','rh','Gestão de servidores, cargos, férias e frequência funcional',4),(6,'Almoxarifado e Patrimônio','almoxarifado','Controle de estoques, requisições internas e bens tombados',4),(7,'Compras e Contratos','compras','Processos licitatórios da Lei 14.133, contratos e fornecedores',5),(8,'Financeiro e Orçamento','financeiro','Execução orçamentária, empenhos, despesas e diárias',5),(9,'Controle Interno','controle','Auditoria interna, matriz de riscos e conformidade',7),(10,'Ouvidoria e SIC','ouvidoria','Gestão de manifestações dos cidadãos e pedidos da Lei de Acesso à Informação',4),(11,'Comunicação Social','comunicacao','Publicação de notícias, pautas e notas institucionais',3);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_agenda_items`
--

DROP TABLE IF EXISTS `session_agenda_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_agenda_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `matter_id` int(11) DEFAULT NULL,
  `order_num` int(11) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pendente',
  `voting_type` varchar(50) NOT NULL DEFAULT 'Nominal',
  `result` varchar(50) DEFAULT 'Não votado',
  `votes_yes` int(11) DEFAULT 0,
  `votes_no` int(11) DEFAULT 0,
  `votes_abstention` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_agenda_session` (`session_id`),
  KEY `fk_agenda_matter` (`matter_id`),
  CONSTRAINT `fk_agenda_matter` FOREIGN KEY (`matter_id`) REFERENCES `legislative_matters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_agenda_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_agenda_items`
--

LOCK TABLES `session_agenda_items` WRITE;
/*!40000 ALTER TABLE `session_agenda_items` DISABLE KEYS */;
INSERT INTO `session_agenda_items` VALUES (1,1,2,1,'Votação em 1º Turno do PL nº 112/2026 (Apoio à Primeira Infância)','Deliberação do parecer da CCJ e mérito do Projeto de Lei Ordinária.','Votado','Nominal','Aprovado por Unanimidade',5,0,0,'2026-10-27 00:49:20'),(2,1,4,2,'Votação da Indicação nº 450/2026 (Asfalto Bairro Universitário)','Apreciação em bloco das indicações encaminhadas ao Poder Executivo.','Votado','Simbólica','Aprovado',5,0,0,'2026-10-27 00:49:20'),(3,2,1,1,'Deliberação do PL nº 104/2026 (Energia Solar)','Discussão e votação do texto original e emendas apresentadas.','Votado','Nominal','Rejeitado',1,2,1,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `session_agenda_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_votes`
--

DROP TABLE IF EXISTS `session_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agenda_item_id` int(11) NOT NULL,
  `councilor_id` int(11) NOT NULL,
  `vote` varchar(20) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_vote_item` (`agenda_item_id`),
  KEY `fk_vote_counc` (`councilor_id`),
  CONSTRAINT `fk_vote_counc` FOREIGN KEY (`councilor_id`) REFERENCES `councilors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vote_item` FOREIGN KEY (`agenda_item_id`) REFERENCES `session_agenda_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_votes`
--

LOCK TABLES `session_votes` WRITE;
/*!40000 ALTER TABLE `session_votes` DISABLE KEYS */;
INSERT INTO `session_votes` VALUES (1,1,1,'Sim','2026-10-27 00:49:20'),(2,1,2,'Sim','2026-10-27 00:49:20'),(3,1,3,'Sim','2026-10-27 00:49:20'),(4,1,4,'Sim','2026-10-27 00:49:20'),(5,1,5,'Sim','2026-10-27 00:49:20'),(6,3,2,'Sim','2026-10-27 00:50:48'),(7,3,1,'Não','2026-10-27 00:50:50'),(8,3,3,'Abstenção','2026-10-27 00:50:51'),(9,3,4,'Não','2026-10-27 00:50:53');
/*!40000 ALTER TABLE `session_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `legislature_id` int(11) NOT NULL,
  `session_number` int(11) NOT NULL,
  `session_type` varchar(50) NOT NULL DEFAULT 'Ordinária',
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Agendada',
  `president_name` varchar(150) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `minutes_text` longtext DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sess_tenant` (`tenant_id`),
  KEY `fk_sess_leg` (`legislature_id`),
  CONSTRAINT `fk_sess_leg` FOREIGN KEY (`legislature_id`) REFERENCES `legislatures` (`id`),
  CONSTRAINT `fk_sess_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,1,1,42,'Ordinária','2026-04-15','09:00:00','Encerrada','Ver. Carlos Eduardo Silva','Apreciação de projetos de interesse social e infraestrutura municipal.','Aos quinze dias do mês de abril do ano de dois mil e vinte e seis, às nove horas, reuniu-se o Plenário da Câmara Municipal sob a presidência do Vereador Carlos Eduardo Silva. Verificado o quórum regimental com a presença de todos os parlamentares, declarou-se aberta a 42ª Sessão Ordinária. Foi aprovada a matéria da Ordem do Dia e nada mais havendo a tratar, encerrou-se a sessão.','https://www.youtube.com/watch?v=dQw4w9WgXcQ','2026-10-27 00:49:20'),(2,1,1,43,'Ordinária','2026-04-22','09:00:00','Agendada','Ver. Carlos Eduardo Silva','Pauta com deliberação de projetos em segundo turno e requerimentos de urgência.','Pauta preliminar em elaboração pela Secretaria Legislativa.',NULL,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_item_id` int(11) NOT NULL,
  `movement_type` enum('ENTRADA','SAIDA','AJUSTE') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `document_ref` varchar(100) DEFAULT NULL,
  `user_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_smov_item` (`material_item_id`),
  CONSTRAINT `fk_smov_item` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,1,'ENTRADA',50,0,50,'Recebimento de entrega NF 18.940 do fornecedor Pantanal Suprimentos','NF-18940','Roberto Guimarães','2026-10-27 00:49:20'),(2,1,'SAIDA',5,50,45,'Atendimento à Requisição REQ-MAT-042 da Secretaria Legislativa','REQ-042','Roberto Guimarães','2026-10-27 00:49:20'),(3,2,'ENTRADA',10,0,10,'Compra direta para reposição emergencial de toners','NF-1201','Roberto Guimarães','2026-10-27 00:49:20'),(4,2,'SAIDA',2,10,8,'Substituição na impressora da Mesa Diretora','REQ-045','Roberto Guimarães','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_requisition_items`
--

DROP TABLE IF EXISTS `stock_requisition_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_requisition_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requisition_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity_requested` int(11) NOT NULL,
  `quantity_delivered` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_reqitem_req` (`requisition_id`),
  KEY `fk_reqitem_mat` (`material_item_id`),
  CONSTRAINT `fk_reqitem_mat` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`id`),
  CONSTRAINT `fk_reqitem_req` FOREIGN KEY (`requisition_id`) REFERENCES `stock_requisitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_requisition_items`
--

LOCK TABLES `stock_requisition_items` WRITE;
/*!40000 ALTER TABLE `stock_requisition_items` DISABLE KEYS */;
INSERT INTO `stock_requisition_items` VALUES (1,1,1,5,5),(2,1,3,2,2),(3,2,1,3,0),(4,2,2,1,0);
/*!40000 ALTER TABLE `stock_requisition_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_requisitions`
--

DROP TABLE IF EXISTS `stock_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_requisitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `requester_name` varchar(150) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Aguardando Aprovação',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_req_tenant` (`tenant_id`),
  CONSTRAINT `fk_req_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_requisitions`
--

LOCK TABLES `stock_requisitions` WRITE;
/*!40000 ALTER TABLE `stock_requisitions` DISABLE KEYS */;
INSERT INTO `stock_requisitions` VALUES (1,1,'REQ-MAT-042','Secretaria Legislativa','Fernanda Rezende','Atendida','Materiais para preparação das pastas de votação das sessões do mês.','2026-10-27 00:49:20'),(2,1,'REQ-MAT-048','Gabinete Parlamentar 02','Mariana Vasconcelos','Aguardando Aprovação','Solicitação de 3 caixas de papel sulfite e 1 toner para confecção de relatórios.','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `stock_requisitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `cnpj_cpf` varchar(20) NOT NULL,
  `corporate_name` varchar(200) NOT NULL,
  `trade_name` varchar(200) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sup_tenant` (`tenant_id`),
  CONSTRAINT `fk_sup_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,1,'18.423.890/0001-44','Pantanal Papéis e Suprimentos de Escritório Eireli','Pantanal Suprimentos','comercial@pantanalsuprimentos.com.br','(67) 3344-9000','Campo Grande','MS','Ativo','2026-10-27 00:49:20'),(2,1,'29.871.234/0001-12','TechMS Soluções em Tecnologia e Sistemas Ltda','TechMS Informática','licita@techms.com.br','(67) 3322-5500','Campo Grande','MS','Ativo','2026-10-27 00:49:20'),(3,1,'08.123.456/0001-78','Auto Posto Alvorada do Centro Ltda','Posto Alvorada','financeiro@postoalvorada.com.br','(67) 3388-1122','Campo Grande','MS','Ativo','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(2) NOT NULL DEFAULT 'MS',
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `coat_of_arms_url` varchar(255) DEFAULT NULL,
  `primary_color` varchar(20) DEFAULT '#0f3460',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (1,'Câmara Municipal de Campo Grande','campo-grande','03.243.511/0001-80','Campo Grande','MS','contato@camara.ms.gov.br','(67) 3316-1500','Avenida Ricardo Brandão, 1600 - Jatiúca Park','https://images.unsplash.com/photo-1590402494682-cd3fb53b1f70?w=120&auto=format&fit=crop&q=80','#0284c7',1,'2026-10-27 00:49:20'),(2,'Câmara Municipal de Bonito','bonito','01.554.212/0001-30','Bonito','MS','atendimento@camarabonito.ms.gov.br','(67) 3255-1122','Rua Coronel Pilad Rebuá, 1820 - Centro','https://images.unsplash.com/photo-1577495508048-b635879837f1?w=120&auto=format&fit=crop&q=80','#059669',1,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_attendances`
--

DROP TABLE IF EXISTS `time_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_attendances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `clock_in` time NOT NULL,
  `lunch_out` time DEFAULT NULL,
  `lunch_in` time DEFAULT NULL,
  `clock_out` time NOT NULL,
  `total_hours` decimal(4,2) DEFAULT 8.00,
  `status` varchar(50) DEFAULT 'Normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_time_emp` (`employee_id`),
  CONSTRAINT `fk_time_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_attendances`
--

LOCK TABLES `time_attendances` WRITE;
/*!40000 ALTER TABLE `time_attendances` DISABLE KEYS */;
INSERT INTO `time_attendances` VALUES (1,1,'2026-04-24','08:02:15','12:01:10','13:05:00','17:03:45',8.00,'Normal','2026-10-27 00:49:20'),(2,3,'2026-04-24','07:58:30','12:00:00','13:00:20','17:00:10',8.00,'Normal','2026-10-27 00:49:20'),(3,4,'2026-04-24','08:15:00','12:00:00','13:00:00','17:15:00',8.00,'Normal','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `time_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_users_tenant` (`tenant_id`),
  KEY `fk_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `fk_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,1,'Dr. Luciano Albuquerque','admin@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','001.234.567-89','Diretor Geral de TI','Tecnologia da Informação',1,'2026-10-26 21:49:52','2026-10-27 00:49:20'),(2,1,2,'Ver. Carlos Eduardo Silva','presidente@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','123.456.789-00','Presidente da Câmara','Mesa Diretora',1,NULL,'2026-10-27 00:49:20'),(3,1,3,'Verª. Mariana Vasconcelos','mariana@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','234.567.890-11','Vereadora','Gabinete Parlamentar 02',1,NULL,'2026-10-27 00:49:20'),(4,1,4,'Fernanda Rezende','secretaria@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','345.678.901-22','Chefe da Secretaria Legislativa','Secretaria Legislativa',1,NULL,'2026-10-27 00:49:20'),(5,1,5,'Marcelo Nogueira','rh@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','456.789.012-33','Coordenador de RH','Recursos Humanos',1,NULL,'2026-10-27 00:49:20'),(6,1,6,'Roberto Guimarães','almoxarife@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','567.890.123-44','Responsável pelo Almoxarifado','Almoxarifado e Patrimônio',1,NULL,'2026-10-27 00:49:20'),(7,1,7,'Patrícia Prado','compras@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','678.901.234-55','Pregoeira e Fiscal de Contratos','Divisão de Compras',1,NULL,'2026-10-27 00:49:20'),(8,1,8,'Gilberto Fontes','financeiro@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','789.012.345-66','Diretor Financeiro','Diretoria Financeira',1,NULL,'2026-10-27 00:49:20'),(9,1,9,'Dra. Vanessa Meireles','controle@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','890.123.456-77','Auditora de Controle Interno','Unidade de Controle Interno',1,NULL,'2026-10-27 00:49:20'),(10,1,10,'Aline Ferreira','ouvidoria@camara.ms.gov.br','$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa','901.234.567-88','Ouvidora Geral','Ouvidoria e SIC',1,NULL,'2026-10-27 00:49:20');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `plate` varchar(20) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `mileage` int(11) NOT NULL DEFAULT 0,
  `driver_name` varchar(150) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Disponível',
  `last_maintenance` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_veh_tenant` (`tenant_id`),
  KEY `fk_veh_asset` (`asset_id`),
  CONSTRAINT `fk_veh_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_veh_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,1,4,'QAA-5F88','Toyota Corolla XEi 2.0',2025,'Flex',18450,'Sérgio Ramos','Disponível','2026-03-15','2026-10-27 00:49:20'),(2,1,NULL,'RBB-9C21','Chevrolet Spin 7 Lugares',2024,'Flex',34200,'Marcos Vinícius','Disponível','2026-02-10','2026-10-27 00:49:20');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'camara_digital_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-26 21:58:15
