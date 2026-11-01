-- ==============================================================
-- CÂMARA DIGITAL - DADOS INICIAIS DE DEMONSTRAÇÃO (SEED)
-- ==============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. TENANTS
INSERT IGNORE INTO `tenants` (`id`, `name`, `slug`, `cnpj`, `city`, `state`, `email`, `phone`, `address`, `coat_of_arms_url`, `primary_color`, `is_active`) VALUES
(1, 'Câmara Municipal de Campo Grande', 'campo-grande', '03.243.511/0001-80', 'Campo Grande', 'MS', 'contato@camara.ms.gov.br', '(67) 3316-1500', 'Avenida Ricardo Brandão, 1600 - Jatiúca Park', 'https://images.unsplash.com/photo-1590402494682-cd3fb53b1f70?w=120&auto=format&fit=crop&q=80', '#0284c7', 1),
(2, 'Câmara Municipal de Bonito', 'bonito', '01.554.212/0001-30', 'Bonito', 'MS', 'atendimento@camarabonito.ms.gov.br', '(67) 3255-1122', 'Rua Coronel Pilad Rebuá, 1820 - Centro', 'https://images.unsplash.com/photo-1577495508048-b635879837f1?w=120&auto=format&fit=crop&q=80', '#059669', 1);

-- 2. ROLES
INSERT IGNORE INTO `roles` (`id`, `name`, `slug`, `description`, `level`) VALUES
(1, 'Administrador Geral', 'admin', 'Acesso total irrestrito a todos os módulos e configurações da Câmara', 10),
(2, 'Presidente / Mesa Diretora', 'mesa', 'Gestão institucional de pautas, ordens do dia, promulgação e atos', 8),
(3, 'Vereador', 'vereador', 'Apresentação de proposições, votação em plenário e gestão de gabinete', 5),
(4, 'Secretaria Legislativa', 'secretaria', 'Controle da tramitação, atas, comissões e redação final', 6),
(5, 'Recursos Humanos', 'rh', 'Gestão de servidores, cargos, férias e frequência funcional', 4),
(6, 'Almoxarifado e Patrimônio', 'almoxarifado', 'Controle de estoques, requisições internas e bens tombados', 4),
(7, 'Compras e Contratos', 'compras', 'Processos licitatórios da Lei 14.133, contratos e fornecedores', 5),
(8, 'Financeiro e Orçamento', 'financeiro', 'Execução orçamentária, empenhos, despesas e diárias', 5),
(9, 'Controle Interno', 'controle', 'Auditoria interna, matriz de riscos e conformidade', 7),
(10, 'Ouvidoria e SIC', 'ouvidoria', 'Gestão de manifestações dos cidadãos e pedidos da Lei de Acesso à Informação', 4),
(11, 'Comunicação Social', 'comunicacao', 'Publicação de notícias, pautas e notas institucionais', 3);

-- 3. USERS (Senha de todos: 123456)
INSERT IGNORE INTO `users` (`id`, `tenant_id`, `role_id`, `name`, `email`, `password_hash`, `cpf`, `job_title`, `department`, `is_active`) VALUES
(1, 1, 1, 'Dr. Luciano Albuquerque', 'admin@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '001.234.567-89', 'Diretor Geral de TI', 'Tecnologia da Informação', 1),
(2, 1, 2, 'Ver. Carlos Eduardo Silva', 'presidente@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '123.456.789-00', 'Presidente da Câmara', 'Mesa Diretora', 1),
(3, 1, 3, 'Verª. Mariana Vasconcelos', 'mariana@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '234.567.890-11', 'Vereadora', 'Gabinete Parlamentar 02', 1),
(4, 1, 4, 'Fernanda Rezende', 'secretaria@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '345.678.901-22', 'Chefe da Secretaria Legislativa', 'Secretaria Legislativa', 1),
(5, 1, 5, 'Marcelo Nogueira', 'rh@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '456.789.012-33', 'Coordenador de RH', 'Recursos Humanos', 1),
(6, 1, 6, 'Roberto Guimarães', 'almoxarife@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '567.890.123-44', 'Responsável pelo Almoxarifado', 'Almoxarifado e Patrimônio', 1),
(7, 1, 7, 'Patrícia Prado', 'compras@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '678.901.234-55', 'Pregoeira e Fiscal de Contratos', 'Divisão de Compras', 1),
(8, 1, 8, 'Gilberto Fontes', 'financeiro@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '789.012.345-66', 'Diretor Financeiro', 'Diretoria Financeira', 1),
(9, 1, 9, 'Dra. Vanessa Meireles', 'controle@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '890.123.456-77', 'Auditora de Controle Interno', 'Unidade de Controle Interno', 1),
(10, 1, 10, 'Aline Ferreira', 'ouvidoria@camara.ms.gov.br', '$2y$10$d9KtJLOwzs7xIMkC0SmUee0uDXZVP7cdx3UQOUsNiNwVIE/OZpBZa', '901.234.567-88', 'Ouvidora Geral', 'Ouvidoria e SIC', 1);

-- 4. DEPARTMENTS
INSERT IGNORE INTO `departments` (`id`, `tenant_id`, `name`, `code`, `manager_name`, `email`) VALUES
(1, 1, 'Presidência e Mesa Diretora', 'PRES', 'Ver. Carlos Eduardo Silva', 'presidencia@camara.ms.gov.br'),
(2, 1, 'Secretaria Legislativa', 'SECLEG', 'Fernanda Rezende', 'secretaria@camara.ms.gov.br'),
(3, 1, 'Diretoria Administrativa', 'DIRADM', 'Cláudio Arruda', 'adm@camara.ms.gov.br'),
(4, 1, 'Divisão de Recursos Humanos', 'DRH', 'Marcelo Nogueira', 'rh@camara.ms.gov.br'),
(5, 1, 'Almoxarifado e Patrimônio', 'ALMOX', 'Roberto Guimarães', 'almoxarifado@camara.ms.gov.br'),
(6, 1, 'Divisão de Licitações e Contratos', 'COMPRAS', 'Patrícia Prado', 'licitacoes@camara.ms.gov.br'),
(7, 1, 'Diretoria Financeira e Contábil', 'FIN', 'Gilberto Fontes', 'financeiro@camara.ms.gov.br'),
(8, 1, 'Unidade de Controle Interno', 'UCI', 'Dra. Vanessa Meireles', 'controle@camara.ms.gov.br'),
(9, 1, 'Ouvidoria e SIC', 'OUV', 'Aline Ferreira', 'ouvidoria@camara.ms.gov.br'),
(10, 1, 'Assessoria de Comunicação', 'ASCOM', 'Juliana Rios', 'comunicacao@camara.ms.gov.br');

-- 5. LEGISLATURES
INSERT IGNORE INTO `legislatures` (`id`, `tenant_id`, `number`, `start_year`, `end_year`, `is_active`) VALUES
(1, 1, 11, 2025, 2028, 1),
(2, 2, 10, 2025, 2028, 1);

-- 6. COUNCILORS
INSERT IGNORE INTO `councilors` (`id`, `tenant_id`, `user_id`, `legislature_id`, `full_name`, `political_party`, `board_role`, `photo_url`, `biography`, `email`, `phone`, `is_active`) VALUES
(1, 1, 2, 1, 'Carlos Eduardo Silva', 'União Brasil', 'Presidente', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&auto=format&fit=crop&q=80', 'Advogado, 3º mandato parlamentar, atuante nas áreas de transparência e infraestrutura.', 'carlos.silva@camara.ms.gov.br', '(67) 99981-1001', 1),
(2, 1, 3, 1, 'Mariana Vasconcelos', 'PSD', 'Vice-Presidente', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80', 'Professora universitária, defensora dos direitos da infância, educação inclusiva e saúde preventiva.', 'mariana.vasconcelos@camara.ms.gov.br', '(67) 99982-2002', 1),
(3, 1, NULL, 1, 'Rodrigo Mendes de Castro', 'MDB', '1º Secretário', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80', 'Empresário e economista, focado na atração de investimentos e simplificação tributária.', 'rodrigo.mendes@camara.ms.gov.br', '(67) 99983-3003', 1),
(4, 1, NULL, 1, 'Dra. Beatriz Toledo', 'PSDB', '2º Secretária', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80', 'Médica cardiologista com atuação comunitária e programas de saúde da família.', 'beatriz.toledo@camara.ms.gov.br', '(67) 99984-4004', 1),
(5, 1, NULL, 1, 'Prof. Lucas Brandão', 'PP', 'Membro', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80', 'Biólogo e professor de ensino médio, pautas voltadas à sustentabilidade e causa animal.', 'lucas.brandao@camara.ms.gov.br', '(67) 99985-5005', 1);

-- 7. COMMITTEES
INSERT IGNORE INTO `committees` (`id`, `tenant_id`, `name`, `description`, `president_name`, `rapporteur_name`) VALUES
(1, 1, 'Comissão de Constituição, Justiça e Redação Final (CCJ)', 'Avalia a legalidade, constitucionalidade e técnica legislativa de todas as matérias.', 'Dra. Beatriz Toledo', 'Carlos Eduardo Silva'),
(2, 1, 'Comissão de Finanças, Orçamento e Fiscalização Financeira', 'Aprecia o plano plurianual, diretrizes orçamentárias e contas públicas.', 'Rodrigo Mendes de Castro', 'Mariana Vasconcelos'),
(3, 1, 'Comissão de Educação, Saúde, Meio Ambiente e Cidadania', 'Foco no acompanhamento de políticas públicas sociais e defesa ambiental.', 'Prof. Lucas Brandão', 'Dra. Beatriz Toledo');

-- 8. LEGISLATIVE MATTERS (Proposições)
INSERT IGNORE INTO `legislative_matters` (`id`, `tenant_id`, `code`, `number`, `year`, `type`, `author_councilor_id`, `author_name`, `title`, `summary`, `full_text`, `status`, `urgency`, `presentation_date`) VALUES
(1, 1, 'PL-104/2026', 104, 2026, 'Projeto de Lei Ordinária', 1, 'Carlos Eduardo Silva', 'Programa Municipal de Energia Solar nos Prédios Públicos', 'Dispõe sobre a obrigatoriedade gradual de instalação de placas fotovoltaicas e sistemas de captação de energia solar em prédios públicos municipais.', 'Art. 1º Fica instituído o Programa Municipal de Energia Solar Fotovoltaica na Administração Pública Direta e Indireta do Município. Art. 2º Novas edificações públicas municipais deverão conter previsão arquitetônica e estrutural para captação de energia limpa. Art. 3º Esta Lei entra em vigor na data de sua publicação.', 'Em Tramitação', 'Normal', '2026-02-10'),
(2, 1, 'PL-112/2026', 112, 2026, 'Projeto de Lei Ordinária', 2, 'Mariana Vasconcelos', 'Programa Municipal de Apoio Integral à Primeira Infância', 'Institui a política municipal integrada para o desenvolvimento integral de crianças de zero a seis anos de idade no município.', 'Art. 1º Fica instituída a Política Municipal de Atenção Integral à Primeira Infância. Art. 2º O Poder Executivo garantirá prioritariamente vagas em creches públicas e acompanhamento pré-natal multiprofissional. Art. 3º Esta Lei entrará em vigor 60 dias após sua publicação oficial.', 'Aprovado', 'Urgência', '2026-03-05'),
(3, 1, 'PLC-015/2026', 15, 2026, 'Projeto de Lei Complementar', 1, 'Carlos Eduardo Silva', 'Modernização do Processo Eletrônico Legislativo e Transparência', 'Altera dispositivos da Lei Orgânica Municipal para regulamentar a assinatura digital qualificada e a tramitação 100% eletrônica de proposições.', 'Art. 1º A tramitação de projetos de lei, vetos e requerimentos no âmbito do Poder Legislativo dar-se-á em suporte eletrônico digital. Art. 2º Os documentos digitais produzidos no sistema oficial possuem fé pública institucional.', 'Em Tramitação', 'Normal', '2026-03-12'),
(4, 1, 'IND-450/2026', 450, 2026, 'Indicação', 3, 'Rodrigo Mendes de Castro', 'Recapeamento Asfáltico e Iluminação LED no Bairro Universitário', 'Indica ao Ilustríssimo Prefeito Municipal a urgente necessidade de pavimentação e substituição de luminárias de vapor por luminárias em LED nas vias principais.', 'O Vereador que a esta subscreve requer, na forma regimental, que seja encaminhado expediente ao Chefe do Poder Executivo, indicando a realização de serviços de recapeamento e iluminação pública no Bairro Universitário.', 'Aprovado', 'Normal', '2026-03-20'),
(5, 1, 'REQ-089/2026', 89, 2026, 'Requerimento', 4, 'Dra. Beatriz Toledo', 'Informações sobre Cronograma de Obras nas UBSs Municipais', 'Requer envio de relatório pormenorizado contendo o cronograma das obras de ampliação e reforma das 12 Unidades Básicas de Saúde da região sul.', 'Requeiro à Mesa Diretora, ouvido o Plenário, que solicite à Secretaria Municipal de Saúde o envio das planilhas de execução orçamentária e cronograma físico das obras em andamento.', 'Aprovado', 'Normal', '2026-04-02');

-- 9. MATTER MOVEMENTS
INSERT IGNORE INTO `matter_movements` (`id`, `matter_id`, `from_dept`, `to_dept`, `dispatch_text`, `status_after`, `movement_date`, `user_name`) VALUES
(1, 1, 'Gabinete Parlamentar 01', 'Secretaria Legislativa', 'Protocolo inicial do Projeto de Lei nº 104/2026 para leitura em sessão plenária.', 'Em Tramitação', '2026-02-10 14:30:00', 'Carlos Eduardo Silva'),
(2, 1, 'Secretaria Legislativa', 'Comissão de Constituição e Justiça', 'Encaminhado à CCJ para elaboração de parecer de admissibilidade e redação final.', 'Em Análise na CCJ', '2026-02-12 10:00:00', 'Fernanda Rezende'),
(3, 2, 'Comissão de Constituição e Justiça', 'Plenário', 'Parecer favorável aprovado por unanimidade. Encaminhado para inclusão na Ordem do Dia.', 'Pronto para Votação', '2026-03-18 16:45:00', 'Dra. Beatriz Toledo');

-- 10. SESSIONS (Sessões Plenárias)
INSERT IGNORE INTO `sessions` (`id`, `tenant_id`, `legislature_id`, `session_number`, `session_type`, `session_date`, `start_time`, `status`, `president_name`, `summary`, `minutes_text`, `video_url`) VALUES
(1, 1, 1, 42, 'Ordinária', '2026-04-15', '09:00:00', 'Encerrada', 'Ver. Carlos Eduardo Silva', 'Apreciação de projetos de interesse social e infraestrutura municipal.', 'Aos quinze dias do mês de abril do ano de dois mil e vinte e seis, às nove horas, reuniu-se o Plenário da Câmara Municipal sob a presidência do Vereador Carlos Eduardo Silva. Verificado o quórum regimental com a presença de todos os parlamentares, declarou-se aberta a 42ª Sessão Ordinária. Foi aprovada a matéria da Ordem do Dia e nada mais havendo a tratar, encerrou-se a sessão.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(2, 1, 1, 43, 'Ordinária', '2026-04-22', '09:00:00', 'Agendada', 'Ver. Carlos Eduardo Silva', 'Pauta com deliberação de projetos em segundo turno e requerimentos de urgência.', 'Pauta preliminar em elaboração pela Secretaria Legislativa.', NULL);

-- 11. SESSION AGENDA ITEMS
INSERT IGNORE INTO `session_agenda_items` (`id`, `session_id`, `matter_id`, `order_num`, `title`, `description`, `status`, `voting_type`, `result`, `votes_yes`, `votes_no`, `votes_abstention`) VALUES
(1, 1, 2, 1, 'Votação em 1º Turno do PL nº 112/2026 (Apoio à Primeira Infância)', 'Deliberação do parecer da CCJ e mérito do Projeto de Lei Ordinária.', 'Votado', 'Nominal', 'Aprovado por Unanimidade', 5, 0, 0),
(2, 1, 4, 2, 'Votação da Indicação nº 450/2026 (Asfalto Bairro Universitário)', 'Apreciação em bloco das indicações encaminhadas ao Poder Executivo.', 'Votado', 'Simbólica', 'Aprovado', 5, 0, 0),
(3, 2, 1, 1, 'Deliberação do PL nº 104/2026 (Energia Solar)', 'Discussão e votação do texto original e emendas apresentadas.', 'Pendente', 'Nominal', 'Não votado', 0, 0, 0);

-- 12. SESSION VOTES
INSERT IGNORE INTO `session_votes` (`id`, `agenda_item_id`, `councilor_id`, `vote`) VALUES
(1, 1, 1, 'Sim'),
(2, 1, 2, 'Sim'),
(3, 1, 3, 'Sim'),
(4, 1, 4, 'Sim'),
(5, 1, 5, 'Sim');

-- 13. LEGAL NORMS (Leis Municipais)
INSERT IGNORE INTO `legal_norms` (`id`, `tenant_id`, `norm_type`, `number`, `year`, `promulgation_date`, `title`, `summary`, `full_text`, `status`, `matter_id`) VALUES
(1, 1, 'Lei Ordinária', 5890, 2026, '2026-01-20', 'Lei Municipal da Mobilidade e Calçadas Acessíveis', 'Estabelece os parâmetros de acessibilidade, padronização e manutenção das calçadas e passeios públicos do município.', 'Art. 1º Todas as calçadas no perímetro urbano deverão observar as normas técnicas de acessibilidade universal NBR 9050. Art. 2º Fica vedada a instalação de obstáculos que impeçam a circulação de pedestres e cadeirantes.', 'Vigente', NULL),
(2, 1, 'Lei Complementar', 412, 2025, '2025-11-14', 'Código Municipal de Edificações Sustentáveis', 'Institui incentivos fiscais (IPTU Verde) para empreendimentos com captação de água de chuva e energia solar.', 'Art. 1º Fica criado o Programa IPTU Verde, concedendo desconto de até 15% no imposto predial para imóveis dotados de tecnologias sustentáveis comprovadas pelo órgão ambiental.', 'Vigente', NULL),
(3, 1, 'Lei Ordinária', 5820, 2025, '2025-06-30', 'Política Municipal de Transparência Ativa e Dados Abertos', 'Regulamenta o acesso irrestrito às informações orçamentárias, financeiras e contratos do município em formato aberto.', 'Art. 1º Os dados produzidos pela Administração Pública Municipal serão disponibilizados em formato legível por máquina (CSV, JSON, XML) no Portal da Transparência em até 48 horas.', 'Vigente', NULL);

-- 14. EMPLOYEES (RH)
INSERT IGNORE INTO `employees` (`id`, `tenant_id`, `full_name`, `cpf`, `registration_number`, `employment_type`, `job_title`, `department`, `salary`, `admission_date`, `status`, `email`, `phone`) VALUES
(1, 1, 'Juliana de Oliveira Rios', '234.987.123-45', 'MAT-1042', 'Efetivo', 'Analista Legislativo de Comunicação', 'Assessoria de Comunicação', 7850.00, '2021-03-01', 'Ativo', 'juliana.rios@camara.ms.gov.br', '(67) 99811-7788'),
(2, 1, 'Renato Sanches Barbosa', '345.876.234-56', 'MAT-1088', 'Comissionado', 'Chefe de Gabinete Parlamentar', 'Gabinete Parlamentar 01', 9200.00, '2025-01-05', 'Ativo', 'renato.sanches@camara.ms.gov.br', '(67) 99822-8899'),
(3, 1, 'Lucas Pires Camargo', '456.765.345-67', 'MAT-1105', 'Efetivo', 'Técnico em Informática', 'Tecnologia da Informação', 5400.00, '2022-08-15', 'Ativo', 'lucas.pires@camara.ms.gov.br', '(67) 99833-9900'),
(4, 1, 'Carla Mendonça Vieira', '567.654.456-78', 'MAT-1011', 'Efetivo', 'Contadora Pública', 'Diretoria Financeira', 11400.00, '2019-02-10', 'Ativo', 'carla.mendonca@camara.ms.gov.br', '(67) 99844-0011'),
(5, 1, 'Thiago Henrique Siqueira', '678.543.567-89', 'MAT-1220', 'Estagiário', 'Estagiário de Direito', 'Secretaria Legislativa', 1600.00, '2026-02-01', 'Ativo', 'thiago.estagio@camara.ms.gov.br', '(67) 99855-1122');

-- 15. LEAVES & VACATIONS
INSERT IGNORE INTO `leaves_vacations` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `status`, `notes`) VALUES
(1, 1, 'Férias Regulamentares', '2026-07-01', '2026-07-20', 'Aprovado', 'Período aquisitivo 2024/2025 deferido pelo RH.'),
(2, 4, 'Licença Maternidade', '2026-05-10', '2026-11-10', 'Aprovado', 'Afastamento legal de 180 dias conforme estatuto dos servidores municipais.');

-- 16. TIME ATTENDANCES
INSERT IGNORE INTO `time_attendances` (`id`, `employee_id`, `work_date`, `clock_in`, `lunch_out`, `lunch_in`, `clock_out`, `total_hours`, `status`) VALUES
(1, 1, '2026-04-24', '08:02:15', '12:01:10', '13:05:00', '17:03:45', 8.00, 'Normal'),
(2, 3, '2026-04-24', '07:58:30', '12:00:00', '13:00:20', '17:00:10', 8.00, 'Normal'),
(3, 4, '2026-04-24', '08:15:00', '12:00:00', '13:00:00', '17:15:00', 8.00, 'Normal');

-- 17. MATERIAL ITEMS (Almoxarifado)
INSERT IGNORE INTO `material_items` (`id`, `tenant_id`, `code`, `name`, `category`, `unit`, `current_stock`, `minimum_stock`, `unit_cost`, `location`) VALUES
(1, 1, 'MAT-001', 'Papel Sulfite A4 75g (Caixa com 10 resmas)', 'Material de Escritório', 'CX', 45, 15, 248.50, 'Prateleira A1'),
(2, 1, 'MAT-002', 'Cartucho de Toner HP LaserJet CF258A', 'Informática e Impressão', 'UN', 8, 4, 385.00, 'Armário Blindado B2'),
(3, 1, 'MAT-003', 'Caneta Esferográfica Azul 1.0mm (Caixa 50 un)', 'Material de Escritório', 'CX', 25, 10, 42.90, 'Prateleira A2'),
(4, 1, 'MAT-004', 'Café Torrado e Moído Tradicional 500g', 'Copa e Cozinha', 'PCT', 32, 20, 22.80, 'Despensa Central'),
(5, 1, 'MAT-005', 'Pasta Suspensa Kraft Completa com Visor (Pct 50)', 'Arquivo e Protocolo', 'PCT', 14, 8, 115.00, 'Prateleira C3'),
(6, 1, 'MAT-006', 'Água Mineral sem Gás 500ml (Fardo c/ 12)', 'Copa e Cozinha', 'FD', 60, 25, 18.00, 'Despensa Central');

-- 18. STOCK MOVEMENTS
INSERT IGNORE INTO `stock_movements` (`id`, `material_item_id`, `movement_type`, `quantity`, `previous_stock`, `new_stock`, `reason`, `document_ref`, `user_name`) VALUES
(1, 1, 'ENTRADA', 50, 0, 50, 'Recebimento de entrega NF 18.940 do fornecedor Pantanal Suprimentos', 'NF-18940', 'Roberto Guimarães'),
(2, 1, 'SAIDA', 5, 50, 45, 'Atendimento à Requisição REQ-MAT-042 da Secretaria Legislativa', 'REQ-042', 'Roberto Guimarães'),
(3, 2, 'ENTRADA', 10, 0, 10, 'Compra direta para reposição emergencial de toners', 'NF-1201', 'Roberto Guimarães'),
(4, 2, 'SAIDA', 2, 10, 8, 'Substituição na impressora da Mesa Diretora', 'REQ-045', 'Roberto Guimarães');

-- 19. STOCK REQUISITIONS
INSERT IGNORE INTO `stock_requisitions` (`id`, `tenant_id`, `code`, `department`, `requester_name`, `status`, `notes`) VALUES
(1, 1, 'REQ-MAT-042', 'Secretaria Legislativa', 'Fernanda Rezende', 'Atendida', 'Materiais para preparação das pastas de votação das sessões do mês.'),
(2, 1, 'REQ-MAT-048', 'Gabinete Parlamentar 02', 'Mariana Vasconcelos', 'Aguardando Aprovação', 'Solicitação de 3 caixas de papel sulfite e 1 toner para confecção de relatórios.');

-- 20. STOCK REQUISITION ITEMS
INSERT IGNORE INTO `stock_requisition_items` (`id`, `requisition_id`, `material_item_id`, `quantity_requested`, `quantity_delivered`) VALUES
(1, 1, 1, 5, 5),
(2, 1, 3, 2, 2),
(3, 2, 1, 3, 0),
(4, 2, 2, 1, 0);

-- 21. SUPPLIERS
INSERT IGNORE INTO `suppliers` (`id`, `tenant_id`, `cnpj_cpf`, `corporate_name`, `trade_name`, `email`, `phone`, `city`, `state`, `status`) VALUES
(1, 1, '18.423.890/0001-44', 'Pantanal Papéis e Suprimentos de Escritório Eireli', 'Pantanal Suprimentos', 'comercial@pantanalsuprimentos.com.br', '(67) 3344-9000', 'Campo Grande', 'MS', 'Ativo'),
(2, 1, '29.871.234/0001-12', 'TechMS Soluções em Tecnologia e Sistemas Ltda', 'TechMS Informática', 'licita@techms.com.br', '(67) 3322-5500', 'Campo Grande', 'MS', 'Ativo'),
(3, 1, '08.123.456/0001-78', 'Auto Posto Alvorada do Centro Ltda', 'Posto Alvorada', 'financeiro@postoalvorada.com.br', '(67) 3388-1122', 'Campo Grande', 'MS', 'Ativo');

-- 22. PROCUREMENTS (Licitações Lei 14.133/21)
INSERT IGNORE INTO `procurements` (`id`, `tenant_id`, `process_number`, `year`, `modality`, `object_description`, `estimated_value`, `status`, `opening_date`, `pncp_published`, `pncp_link`) VALUES
(1, 1, 'PE-004/2026', 2026, 'Pregão Eletrônico (Lei 14.133)', 'Contratação de empresa especializada em fornecimento de materiais permanentes de TI e servidores.', 145000.00, 'Homologado', '2026-02-15', 1, 'https://pncp.gov.br/app/editais/03243511000180/2026/000004'),
(2, 1, 'DL-012/2026', 2026, 'Dispensa de Licitação (Art. 75, II)', 'Aquisição contínua de suprimentos de escritório e papelaria para consumo anual dos gabinetes.', 48900.00, 'Homologado', '2026-01-28', 1, 'https://pncp.gov.br/app/editais/03243511000180/2026/000012'),
(3, 1, 'PE-008/2026', 2026, 'Pregão Eletrônico (Lei 14.133)', 'Prestação de serviços contínuos de transmissão e gravação audiovisual das sessões plenárias.', 88000.00, 'Em Andamento', '2026-05-02', 1, 'https://pncp.gov.br/app/editais/03243511000180/2026/000008');

-- 23. CONTRACTS
INSERT IGNORE INTO `contracts` (`id`, `tenant_id`, `procurement_id`, `supplier_id`, `contract_number`, `year`, `object_summary`, `total_value`, `start_date`, `end_date`, `manager_name`, `inspector_name`, `status`) VALUES
(1, 1, 1, 2, 'CONT-008', 2026, 'Fornecimento e manutenção preventiva dos equipamentos de infraestrutura de rede e servidores de dados.', 139800.00, '2026-03-01', '2027-02-28', 'Dr. Luciano Albuquerque', 'Lucas Pires Camargo', 'Vigente'),
(2, 1, 2, 1, 'CONT-011', 2026, 'Fornecimento parcelado de papéis, toners e materiais de escritório para a Câmara Municipal.', 46500.00, '2026-02-01', '2027-01-31', 'Cláudio Arruda', 'Roberto Guimarães', 'Vigente');

-- 24. ASSETS (Patrimônio)
INSERT IGNORE INTO `assets` (`id`, `tenant_id`, `asset_tag`, `description`, `category`, `brand`, `serial_number`, `acquisition_date`, `acquisition_value`, `current_value`, `location`, `responsible_name`, `condition_status`) VALUES
(1, 1, 'PAT-2026-001', 'Servidor Rack Dell PowerEdge R650 64GB Xeon', 'Informática e Servidores', 'Dell', 'SERV-DELL-9842A', '2026-03-05', 38500.00, 36000.00, 'Data Center / CPD', 'Dr. Luciano Albuquerque', 'Novo'),
(2, 1, 'PAT-2025-045', 'Mesa de Som Digital 24 Canais Plenário', 'Equipamentos Audiovisuais', 'Yamaha', 'YAM-TF3-4819', '2025-04-12', 19800.00, 16500.00, 'Cabine de Som do Plenário', 'Juliana Rios', 'Bom'),
(3, 1, 'PAT-2024-112', 'Mesa Diretora em Madeira Nobre com 5 Postos', 'Móveis e Utensílios', 'Marcenaria Central', 'MOB-PLEN-001', '2024-01-10', 14200.00, 11000.00, 'Plenário Principal', 'Carlos Eduardo Silva', 'Bom'),
(4, 1, 'PAT-2025-089', 'Veículo Oficial Sedan Toyota Corolla 2.0 Flex', 'Veículos e Frota', 'Toyota', 'CHAS-9BR-2025-99', '2025-08-20', 148000.00, 135000.00, 'Garagem Oficial', 'Sérgio Ramos (Motorista)', 'Bom');

-- 25. VEHICLES (Frota)
INSERT IGNORE INTO `vehicles` (`id`, `tenant_id`, `asset_id`, `plate`, `model`, `year`, `fuel_type`, `mileage`, `driver_name`, `status`, `last_maintenance`) VALUES
(1, 1, 4, 'QAA-5F88', 'Toyota Corolla XEi 2.0', 2025, 'Flex', 18450, 'Sérgio Ramos', 'Disponível', '2026-03-15'),
(2, 1, NULL, 'RBB-9C21', 'Chevrolet Spin 7 Lugares', 2024, 'Flex', 34200, 'Marcos Vinícius', 'Disponível', '2026-02-10');

-- 26. BUDGET ALLOCATIONS (Orçamento)
INSERT IGNORE INTO `budget_allocations` (`id`, `tenant_id`, `code`, `description`, `initial_budget`, `committed_budget`, `realized_budget`, `fiscal_year`) VALUES
(1, 1, '01.031.0001.2001', 'Manutenção Geral e Operacional do Poder Legislativo', 12500000.00, 4200000.00, 3100000.00, 2026),
(2, 1, '01.031.0001.2002', 'Capacitação e Desenvolvimento de Servidores e Parlamentares', 350000.00, 85000.00, 62000.00, 2026),
(3, 1, '01.031.0001.2003', 'Modernização Tecnológica e Transformação Digital', 980000.00, 320000.00, 240000.00, 2026);

-- 27. EXPENSE REQUESTS (Despesas e Diárias)
INSERT IGNORE INTO `expense_requests` (`id`, `tenant_id`, `budget_allocation_id`, `expense_type`, `beneficiary_name`, `description`, `amount`, `request_date`, `status`, `payment_date`) VALUES
(1, 1, 2, 'Diária de Viagem', 'Carlos Eduardo Silva', 'Participação no Encontro Nacional de Presidentes de Câmaras Municipais em Brasília-DF.', 2800.00, '2026-03-10', 'Paga', '2026-03-14'),
(2, 1, 3, 'Serviço de Terceiros TI', 'TechMS Soluções Ltda', 'Pagamento da medição 01 do contrato de infraestrutura de TI.', 11650.00, '2026-04-01', 'Paga', '2026-04-05'),
(3, 1, 1, 'Consumo de Combustível', 'Auto Posto Alvorada', 'Abastecimento mensal da frota oficial de veículos da Câmara.', 4320.50, '2026-04-10', 'Autorizada', NULL);

-- 28. PROTOCOLS (Protocolo Geral)
INSERT IGNORE INTO `protocols` (`id`, `tenant_id`, `protocol_number`, `year`, `sender_name`, `sender_type`, `subject`, `summary`, `destination_department`, `urgency`, `status`, `verification_hash`) VALUES
(1, 1, 'PROT-2026/00142', 2026, 'Associação de Moradores do Bairro Pioneiros', 'Cidadão', 'Reivindicação de Implantação de Área de Lazer e Academia ao Ar Livre', 'Solicita apoio do Poder Legislativo junto à Prefeitura para urbanização de praça pública.', 'Gabinete da Presidência', 'Normal', 'Despachado', 'a3f5c78912e4b6c8912301fae87901234567890abcdef1234567890abcdef123'),
(2, 1, 'PROT-2026/00155', 2026, 'Tribunal de Contas do Estado (TCE-MS)', 'Órgão Externo', 'Notificação de Remessa das Contas do Exercício Anterior', 'Encaminha ofício de confirmação de recebimento da prestação de contas digital da Câmara.', 'Unidade de Controle Interno', 'Alta', 'Em Análise', 'b8e9f0123456789abcdef1234567890abcdef1234567890abcdef1234567890a'),
(3, 1, 'PROT-2026/00168', 2026, 'Sindicato dos Servidores Municipais', 'Entidade de Classe', 'Proposta de Reajuste do Auxílio Alimentação para o Exercício 2026', 'Minuta de reivindicação salarial e benefícios funcionais para apreciação da Mesa Diretora.', 'Diretoria Administrativa', 'Normal', 'Aberto', 'c9a0b1234567890abcdef1234567890abcdef1234567890abcdef1234567890b');

-- 29. PROTOCOL DISPATCHES
INSERT IGNORE INTO `protocol_dispatches` (`id`, `protocol_id`, `dispatch_text`, `user_name`, `to_department`) VALUES
(1, 1, 'Recebido e cadastrado no protocolo geral. Encaminhado à Presidência para análise e despacho.', 'Aline Ferreira', 'Presidência e Mesa Diretora'),
(2, 1, 'Ciente. Encaminhado à Secretaria Legislativa para transformar em Indicação parlamentar.', 'Carlos Eduardo Silva', 'Secretaria Legislativa');

-- 30. CITIZEN REQUESTS (Ouvidoria e SIC)
INSERT IGNORE INTO `citizen_requests` (`id`, `tenant_id`, `protocol_code`, `request_type`, `citizen_name`, `citizen_email`, `citizen_phone`, `is_anonymous`, `subject`, `message`, `status`, `reply_text`, `replied_at`, `replied_by`, `deadline_date`) VALUES
(1, 1, 'OUV-2026-10492', 'SIC - Pedido LAI', 'Mateus Cavalcanti Neves', 'mateus.neves@gmail.com', '(67) 99123-4567', 0, 'Detalhamento dos Gastos com Diárias do 1º Trimestre de 2026', 'Solicito a planilha detalhada contendo nome dos parlamentares e servidores que receberam diárias no período de janeiro a março de 2026, com destino e justificativa.', 'Respondido', 'Prezado cidadão, informamos que os dados solicitados já se encontram integralmente publicados no Portal da Transparência da Câmara, na aba "Diárias e Despesas". Disponibilizamos em anexo a planilha consolidada em formato aberto CSV.', '2026-04-12 11:20:00', 'Aline Ferreira (Ouvidora)', '2026-04-20'),
(2, 1, 'OUV-2026-10515', 'Reclamação', 'Cidadão Anônimo', NULL, NULL, 1, 'Demora no início da transmissão ao vivo da última sessão', 'Gostaria de registrar que a transmissão no canal do YouTube começou com 25 minutos de atraso na última terça-feira, perdendo a leitura da ata.', 'Recebido', NULL, NULL, NULL, '2026-05-05'),
(3, 1, 'OUV-2026-10530', 'Sugestão', 'Helena Beatriz Martins', 'helena.martins@outlook.com', '(67) 99234-5678', 0, 'Criação do Programa Câmara Jovem para estudantes de escolas públicas', 'Sugiro que a Câmara promova uma edição anual da Câmara Jovem para incentivar a cidadania e o debate democrático entre estudantes da nossa cidade.', 'Em Tramitação', NULL, NULL, NULL, '2026-05-10');

-- 31. INTERNAL CONTROLS (Riscos e Achados)
INSERT IGNORE INTO `internal_controls` (`id`, `tenant_id`, `title`, `category`, `risk_level`, `finding_description`, `recommendation`, `action_plan`, `responsible_name`, `deadline`, `status`) VALUES
(1, 1, 'Aderência às Publicações Obrigatórias no PNCP (Lei 14.133)', 'Legal/Regulamentar', 'Alto', 'Identificada necessidade de automação de sincronização dos avisos de dispensa com a API nacional do PNCP.', 'Adotar conector oficial e checklist obrigatório antes da emissão do termo de homologação.', 'Implementar trava de sistema que exige código de publicação PNCP para emissão da nota de empenho.', 'Patrícia Prado (Compras)', '2026-05-30', 'Em Andamento'),
(2, 1, 'Segurança e Isolamento de Dados Pessoais de Ouvidoria (LGPD)', 'TI / Privacidade', 'Médio', 'Verificar se denúncias com solicitação de sigilo ocultam os dados de contato em relatórios públicos.', 'Garantir segregação técnica de manifestações anônimas ou sigilosas no banco de dados e APIs.', 'Concluída a revisão de permissões dos perfis de atendimento com aprovação da DPO.', 'Dra. Vanessa Meireles (Controle)', '2026-04-15', 'Concluído');

-- 32. NEWS (Comunicação)
INSERT IGNORE INTO `news` (`id`, `tenant_id`, `title`, `slug`, `subtitle`, `content`, `category`, `author_name`, `image_url`, `published_at`, `is_published`) VALUES
(1, 1, 'Câmara aprova por unanimidade projeto de apoio à Primeira Infância', 'camara-aprova-projeto-primeira-infancia', 'Proposição garante ampliação de vagas em creches e acompanhamento integrado para crianças.', 'Em sessão plenária histórica realizada nesta quarta-feira, os vereadores aprovaram por unanimidade o Projeto de Lei nº 112/2026. A proposta institui diretrizes para a política municipal da primeira infância, unindo esforços das secretarias de educação e saúde.', 'Legislativo', 'Assessoria de Comunicação', 'https://images.unsplash.com/photo-1577495508048-b635879837f1?w=600&auto=format&fit=crop&q=80', '2026-04-16 10:00:00', 1),
(2, 1, 'Portal da Transparência atinge nota máxima em avaliação dos órgãos de controle', 'portal-transparencia-nota-maxima', 'Avaliação independente reconheceu a facilidade de acesso a dados em formato aberto e prazos de resposta da Ouvidoria.', 'A modernização digital da Câmara Municipal resultou na obtenção do Selo Diamante de Transparência Pública. O cidadão agora tem acesso imediato a todas as contas, contratos e leis aprovadas com apenas um clique.', 'Institucional', 'Assessoria de Comunicação', 'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&auto=format&fit=crop&q=80', '2026-04-20 14:30:00', 1),
(3, 1, 'Audiência Pública debaterá modernização da iluminação e energia solar no município', 'audiencia-publica-energia-solar', 'Encontro aberto à população será realizado no Plenário na próxima quinta-feira.', 'A Presidência da Câmara convida toda a comunidade, empresários e especialistas do setor energético para debater o Projeto de Lei nº 104/2026, que incentiva a instalação de usinas solares em prédios públicos.', 'Sessões', 'Assessoria de Comunicação', 'https://images.unsplash.com/photo-1508873696983-2df5293cb32f?w=600&auto=format&fit=crop&q=80', '2026-04-22 09:00:00', 1);

-- 33. AI CONVERSATIONS (Logs de IA)
INSERT IGNORE INTO `ai_conversations` (`id`, `tenant_id`, `user_id`, `module_context`, `prompt`, `response`, `sources_cited`, `latency_ms`, `user_feedback`) VALUES
(1, 1, 1, 'Legislativo', 'Quais leis municipais tratam sobre sustentabilidade e incentivo ao meio ambiente?', 'No acervo municipal foram localizadas duas normas principais relacionadas:\n1. **Lei Complementar nº 412/2025** (Código de Edificações Sustentáveis) - Cria o IPTU Verde com desconto de até 15% para captação solar e reúso de água.\n2. **Projeto de Lei nº 104/2026** (em tramitação) - Institui a obrigatoriedade de energia solar nos prédios públicos municipais.', 'Lei Complementar nº 412/2025; Projeto de Lei nº 104/2026', 420, 'positivo'),
(2, 1, 2, 'Redação', 'Gerar justificativa fundamentada para Projeto de Lei sobre calçadas acessíveis.', 'A justificativa apoia-se no princípio constitucional da dignidade da pessoa humana e nas diretrizes da Lei Federal de Acessibilidade (Lei 10.098/2000). A padronização dos passeios públicos garante o direito de ir e vir a pessoas idosas e com deficiência, prevenindo acidentes urbanos.', 'Lei Federal nº 10.098/2000; NBR 9050', 380, 'positivo');

-- 34. AUDIT LOGS
INSERT IGNORE INTO `audit_logs` (`id`, `tenant_id`, `user_id`, `user_name`, `action`, `entity`, `entity_id`, `details`, `ip_address`) VALUES
(1, 1, 1, 'Dr. Luciano Albuquerque', 'LOGIN', 'users', 1, 'Login efetuado com sucesso via credencial administrativa.', '127.0.0.1'),
(2, 1, 2, 'Ver. Carlos Eduardo Silva', 'CRIAR_PROPOSICAO', 'legislative_matters', 1, 'Apresentação formal do Projeto de Lei nº 104/2026 no sistema.', '127.0.0.1'),
(3, 1, 4, 'Fernanda Rezende', 'PUBLICAR_SESSAO', 'sessions', 1, 'Publicação oficial da ata e resultado de votação da 42ª Sessão Ordinária.', '127.0.0.1');

SET FOREIGN_KEY_CHECKS = 1;
