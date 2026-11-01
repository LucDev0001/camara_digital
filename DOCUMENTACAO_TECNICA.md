# 🛠️ Câmara Digital — Documentação Técnica para Desenvolvedores

Esta documentação técnica fornece uma visão aprofundada da arquitetura, do modelo de dados relacional, da camada de segurança, dos pipelines de Inteligência Artificial (RAG) e dos padrões de extensibilidade da plataforma **Câmara Digital**.

---

## 📑 Índice da Documentação

1. [Visão Geral da Arquitetura](#1-visão-geral-da-arquitetura)
2. [Estrutura de Diretórios e Arquivos](#2-estrutura-de-diretórios-e-arquivos)
3. [Mecanismo Multi-Tenant (Segregação de Dados)](#3-mecanismo-multi-tenant-segregação-de-dados)
4. [Dicionário de Dados e Modelo Relacional (37 Tabelas)](#4-dicionário-de-dados-e-modelo-relacional-37-tabelas)
5. [Camada de Segurança, Autenticação e RBAC](#5-camada-de-segurança-autenticação-e-rbac)
6. [Motor de Inteligência Artificial e Pipeline RAG](#6-motor-de-inteligência-artificial-e-pipeline-rag)
7. [Especificação das APIs REST e Endpoints AJAX](#7-especificação-das-apis-rest-e-endpoints-ajax)
8. [Engenharia do Sistema de Backup e Recuperação (DR)](#8-engenharia-do-sistema-de-backup-e-recuperação-dr)
9. [Guia de Extensibilidade: Criando um Novo Módulo](#9-guia-de-extensibilidade-criando-um-novo-módulo)
10. [Instalação, Ambientes e Comandos de Manutenção](#10-instalação-ambientes-e-comandos-de-manutenção)
11. [Conformidade Regulatória v1.2, PNTP 2026 e Linguagem Simples](#11-conformidade-regulatória-v12-pntp-2026-e-linguagem-simples)

---

## 1. Visão Geral da Arquitetura

O sistema **Câmara Digital** adota uma arquitetura **Modular MVC em PHP 8.2+ Nativo** com foco em altíssimo desempenho, ausência de dependências externas pesadas e tempo de resposta inferior a 100 milissegundos.

### 1.1 Diagrama de Camadas da Aplicação

```
┌────────────────────────────────────────────────────────────────────────┐
│                   INTERFACE DO USUÁRIO & PORTAIS                       │
│  - Portal Público do Cidadão (HTML5 + Tailwind CSS + Vanilla JS)       │
│  - Painel Administrativo / Plenário em Tempo Real                      │
└──────────────────────────────────┬─────────────────────────────────────┘
                                   │ HTTP Requests / Fetch AJAX
┌──────────────────────────────────▼─────────────────────────────────────┐
│                    ROTEAMENTO & CONTROLADORES                          │
│  - /portal/*.php (Páginas Cidadãs Livres)                              │
│  - /modulos/*.php (12 Módulos Funcionais Protegidos por RBAC)          │
│  - /api/*.php (Endpoints REST/JSON com CSRF e Auth Tokens)             │
└──────────────────────────────────┬─────────────────────────────────────┘
                                   │ Invocação de Serviços
┌──────────────────────────────────▼─────────────────────────────────────┐
│                       CAMADA DE SERVIÇOS CORE                          │
│  - Auth.php (Sessões, BCRYPT, Permissões RBAC)                         │
│  - Tenant.php (Multi-Inquilinato e Contexto Municipal)                 │
│  - AIAssistant.php (Pipeline RAG, Heurísticas e Redação LC 95/98)      │
│  - Backup.php (Dump Híbrido, SHA-256 e Disaster Recovery)              │
│  - Audit.php (Trilha de Auditoria com IP, User e Snapshot)             │
└──────────────────────────────────┬─────────────────────────────────────┘
                                   │ Abstração PDO
┌──────────────────────────────────▼─────────────────────────────────────┐
│                      BANCO DE DADOS (DATABASE)                         │
│  - MySQL 8.0+ / MariaDB 10.4+ (Charset utf8mb4_unicode_ci)             │
│  - 34 Tabelas Relacionais com Chaves Estrangeiras (InnoDB)             │
│  - Índices de Busca Global e Integridade Referencial                   │
└────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Stack Tecnológica
- **Backend**: PHP 8.2+ (PDO com conexões persistentes, tipagem estrita e classes estáticas de utilitários).
- **Banco de Dados**: MySQL 8.0+ / MariaDB 10.4+ (Storage Engine `InnoDB`, charset `utf8mb4`, collation `utf8mb4_unicode_ci`).
- **Frontend**: Tailwind CSS (CDN oficial compilada), FontAwesome 6 Pro/Free para iconografia vetorial e Vanilla JS sem dependências para operações de DOM e requisições assíncronas.
- **Auditoria e Criptografia**: Algoritmo `SHA-256` para assinatura de protocolos e integridade de backups; `BCRYPT` com fator de custo 10 para senhas de usuários.

---

## 2. Estrutura de Diretórios e Arquivos

```
camara_digital/
├── config/
│   ├── database.php        # Gerenciador de conexão PDO Singleton com reconexão
│   └── app.php             # Configurações globais, helpers, CSRF e sanitização
├── includes/
│   ├── Auth.php            # Autenticação, controle de papéis (RBAC) e sessões
│   ├── Tenant.php          # Contextualização do inquilino ativo (tenant_id)
│   ├── Audit.php           # Trilha de auditoria imutável (Audit Log)
│   ├── AIAssistant.php     # Motor de RAG, sumarização e redação de projetos de lei
│   ├── Backup.php          # Motor de backup (mysqldump + dumper nativo PDO)
│   ├── Header.php          # Topbar administrativa responsiva com menu de perfil
│   ├── Sidebar.php         # Navegação lateral unificada com indicadores de rotas
│   └── Footer.php          # Rodapé de conformidade e scripts JS globais
├── modulos/                # As 12 Áreas Funcionais Administrativas
│   ├── administracao.php   # Área 1: Mesa Diretora, Comissões e Atos Oficiais
│   ├── legislativo.php     # Área 2: Processo Legislativo e Painel Plenário
│   ├── rh.php              # Área 3: Recursos Humanos, Cargos e Folha
│   ├── almoxarifado.php    # Área 4: Controle de Estoque com travas atômicas
│   ├── compras.php         # Área 5: Licitações (Lei 14.133/21) e Contratos
│   ├── patrimonio.php      # Área 6: Bens Tombados e Gestão de Frota
│   ├── financeiro.php      # Área 7: Despesas (Empenho/Liq/Pag) e Duodécimo
│   ├── transparencia.php   # Área 8: Gestão de Transparência e RGF
│   ├── ouvidoria.php       # Área 9: Atendimento de Manifestações e SIC Interno
│   ├── protocolo.php       # Área 10: Protocolo Digital com Hashes SHA-256
│   ├── controle_interno.php# Área 11: Matriz de Riscos e Auditoria
│   ├── ia_legislativa.php  # Área 12: Terminal de IA e Busca Semântica
│   └── backup.php          # Painel de Gestão e Restauração de Backups
├── portal/                 # Portal Público de Acesso Livre ao Cidadão
│   ├── index.php           # Home pública com busca integrada
│   ├── leis.php            # Repositório público de legislação municipal
│   ├── proposicoes.php     # Consulta de proposições e tramitação
│   ├── sessoes.php         # Pautas, atas e votações nominais de plenário
│   ├── transparencia.php   # Portal da Transparência Ativa ao Cidadão
│   ├── ouvidoria.php       # Formulário Fala.BR e consulta de protocolos
│   ├── validar.php         # Validador público de autenticidade por Hash SHA-256
│   └── noticias.php        # Noticiário institucional e comunicados
├── api/                    # Endpoints de API REST / AJAX
│   ├── ai_assistant.php    # Processamento RAG, geração de minutas e pautas
│   ├── vote_plenary.php    # Votação atômica de vereadores em plenário
│   ├── stock_action.php    # Movimentação atômica de estoque (saídas/entradas)
│   ├── protocol_action.php # Geração de protocolo e despachos
│   ├── citizen_action.php  # Abertura de manifestações pelo cidadão
│   ├── switch_tenant.php   # Alternância de tenant pelo administrador
│   └── quick_search.php    # Busca global rápida via JSON
├── backups/                # Diretório seguro de arquivos .sql e metadados .json
│   └── .htaccess           # Proteção contra download direto via navegador
├── sql/
│   ├── schema.sql          # DDL completo das 34 tabelas e relacionamentos
│   └── seed.sql            # Carga completa de dados demonstrativos e legislativos
├── backup.php              # Utilitário CLI para crons e rotinas agendadas
├── install.php             # Instalador e migrador automático do banco de dados
├── login.php               # Interface de autenticação com validação CSRF
├── logout.php              # Destruição segura de sessão e desautenticação
└── dashboard.php           # Painel de controle central de indicadores e alertas
```

---

## 3. Mecanismo Multi-Tenant (Segregação de Dados)

O sistema emprega a abordagem **Single-Database Multi-Tenant with Shared Schema**, na qual todas as tabelas transacionais e cadastrais contêm uma coluna identificadora `tenant_id`:

### 3.1 Classe de Contexto: `Tenant.php`
```php
class Tenant {
    public static function id(): int {
        return (int)($_SESSION['tenant_id'] ?? 1);
    }

    public static function current(): array {
        // Retorna os dados cadastrais da Câmara ativa (CNPJ, Brasão, Município)
    }

    public static function set(int $tenantId): void {
        // Valida se o tenant existe e atualiza a sessão do usuário
    }
}
```

### 3.2 Padrão de Consultas Isoladas
Todas as queries do sistema que manipulam dados sensíveis devem incorporar a cláusula `WHERE tenant_id = ?`:
```php
$stmt = $pdo->prepare("SELECT * FROM proposicoes WHERE tenant_id = ? AND status = ?");
$stmt->execute([Tenant::id(), 'em_tramitacao']);
```
Esse isolamento lógico garante que uma Câmara Municipal nunca visualize ou sobrescreva informações pertencentes a outro município, viabilizando o modelo SaaS governamental.

---

## 4. Dicionário de Dados e Modelo Relacional (37 Tabelas)

O banco de dados `camara_digital_db` contém 37 tabelas relacionais organizadas em núcleos funcionais e de governança:

```
                                  ┌───────────────────┐
                                  │      tenants      │
                                  └─────────┬─────────┘
                                            │ 1:N
        ┌───────────────────┬───────────────┼───────────────┬───────────────────┐
        ▼                   ▼               ▼               ▼                   ▼
┌───────────────┐   ┌───────────────┐┌───────────────┐┌───────────────┐ ┌───────────────┐
│     users     │   │   vereadores  ││  proposicoes  ││sessoes_plenari│ │ almox_itens   │
└───────┬───────┘   └───────┬───────┘└───────┬───────┘└───────┬───────┘ └───────┬───────┘
        │                   │                │                │                 │
        │ 1:N               │ 1:N            │ 1:N            │ 1:N             │ 1:N
        ▼                   ▼                ▼                ▼                 ▼
┌───────────────┐   ┌────────────────┐┌──────────────┐┌───────────────┐ ┌───────────────┐
│  audit_logs   │   │ plen_votacoes  ││  tramitacao  ││sessoes_pautas │ │ almox_movim   │
└───────────────┘   └────────────────┘└──────────────┘└───────────────┘ └───────────────┘
```

### 4.1 Relação Completa de Tabelas por Módulo

| Nº | Nome da Tabela | Finalidade Principal | Chaves Estrangeiras (FKs) |
| :---: | :--- | :--- | :--- |
| **1** | `tenants` | Cadastro das Câmaras Municipais cadastradas no sistema | N/A (Root) |
| **2** | `users` | Usuários do sistema administrativo e perfis de acesso | `tenant_id -> tenants(id)` |
| **3** | `mesa_diretora` | Membros da Mesa Diretora por biênio legislativo | `tenant_id`, `vereador_id -> vereadores(id)` |
| **4** | `comissoes` | Comissões permanentes e temporárias da Casa | `tenant_id` |
| **5** | `comissoes_membros`| Parlamentares vinculados a cada comissão e seus cargos | `comissao_id`, `vereador_id` |
| **6** | `atos_oficiais` | Decretos legislativos, portarias e resoluções da presidência | `tenant_id`, `autor_id -> users(id)` |
| **7** | `vereadores` | Cadastro oficial de parlamentares da legislatura | `tenant_id`, `user_id -> users(id)` |
| **8** | `proposicoes` | Matérias legislativas (PLs, Requerimentos, Indicações) | `tenant_id`, `autor_id -> vereadores(id)` |
| **9** | `proposicoes_tramit`| Histórico e árvore de tramitação das matérias | `proposicao_id -> proposicoes(id)` |
| **10**| `sessoes_plenarias`| Sessões ordinárias, extraordinárias e solenes | `tenant_id`, `presidente_id -> vereadores(id)` |
| **11**| `sessoes_pautas` | Matérias que compõem a Ordem do Dia da sessão | `sessao_id`, `proposicao_id` |
| **12**| `plen_votacoes` | Votos nominais registrados pelos vereadores no plenário | `sessao_id`, `proposicao_id`, `vereador_id` |
| **13**| `rh_servidores` | Servidores efetivos, comissionados e agentes públicos | `tenant_id`, `user_id` |
| **14**| `rh_cargos` | Estrutura de cargos, vencimentos e padrões remuneratórios | `tenant_id` |
| **15**| `rh_folha` | Folha de pagamento mensal e contracheques | `tenant_id`, `servidor_id -> rh_servidores(id)` |
| **16**| `almox_categorias`| Categorias de materiais de consumo e suprimentos | `tenant_id` |
| **17**| `almox_itens` | Catálogo de materiais, estoque atual e estoque mínimo | `tenant_id`, `categoria_id` |
| **18**| `almox_movim` | Histórico atômico de entradas e requisições de saída | `item_id -> almox_itens(id)`, `user_id` |
| **19**| `compras_processos`| Processos de contratação sob a Lei nº 14.133/2021 | `tenant_id`, `responsavel_id -> users(id)` |
| **20**| `compras_fases` | Fases da licitação (ETP, TR, Julgamento, Homologação) | `processo_id -> compras_processos(id)` |
| **21**| `contratos` | Contratos administrativos, vigência e valores totais | `tenant_id`, `processo_id` |
| **22**| `contratos_aditivos`| Aditivos de tempo ou valor dos contratos | `contrato_id -> contratos(id)` |
| **23**| `patrimonio_bens` | Bens móveis e imóveis tombados, valores e depreciação | `tenant_id`, `responsavel_id -> rh_servidores(id)` |
| **24**| `frota_veiculos` | Veículos oficiais da Câmara (placa, marca, hodômetro) | `tenant_id` |
| **25**| `frota_viagens` | Registro de missões oficiais e controle de abastecimento | `veiculo_id`, `motorista_id -> rh_servidores(id)` |
| **26**| `fin_duodecimo` | Controle mensal do duodécimo repassado pelo Executivo | `tenant_id` |
| **27**| `fin_despesas` | Execução orçamentária (Empenho, Liquidação, Pagamento) | `tenant_id`, `fornecedor_id` |
| **28**| `transp_publicacoes`| Publicações de conformidade e relatórios fiscais (RGF) | `tenant_id`, `publicado_por -> users(id)` |
| **29**| `ouvidoria_manif` | Manifestações do Fala.BR (Denúncias, Elogios, LAI) | `tenant_id` |
| **30**| `ouvidoria_respostas`| Respostas e prorrogações do Ouvidor ao cidadão | `manifestacao_id`, `respondido_por -> users(id)` |
| **31**| `protocolos` | Protocolo geral e geração de hashes SHA-256 | `tenant_id`, `criado_por -> users(id)` |
| **32**| `protocolo_tramit` | Movimentação de processos administrativos entre setores | `protocolo_id -> protocolos(id)` |
| **33**| `ctrl_riscos` | Matriz de riscos do Controle Interno e apontamentos | `tenant_id`, `auditor_id -> users(id)` |
| **34**| `audit_logs` | Trilha de auditoria imutável de todas as ações no sistema | `tenant_id`, `user_id -> users(id)` |
| **35**| `tenant_compliance_profiles`| Perfil de conformidade jurídica, LOM, Regimento Interno e Diário Oficial | `tenant_id -> tenants(id)` |
| **36**| `pntp_checklist` | Critérios de preparação e auditoria PNTP 2026 (Radar da Transparência Atricon) | `tenant_id -> tenants(id)` |
| **37**| `carta_servicos` | Catálogo e Carta de Serviços ao Cidadão (Lei Federal nº 13.460/2017) | `tenant_id -> tenants(id)` |

---

## 5. Camada de Segurança, Autenticação e RBAC

### 5.1 Autenticação e Hashing de Senhas
A classe `includes/Auth.php` implementa autenticação segura:
```php
// Criação segura de senhas
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

// Verificação no momento do login
if (password_verify($password, $user['password_hash'])) {
    // Regeneração de ID de sessão contra session fixation
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['tenant_id'] = $user['tenant_id'];
    $_SESSION['user_role'] = $user['role'];
}
```

### 5.2 Controle de Acesso Baseado em Perfis (RBAC)
Cada usuário possui um papel atribuído (`role`):
- `admin`: Controle irrestrito sobre o sistema, parâmetros, usuários e backups.
- `vereador`: Apresentação de matérias e votação no plenário.
- `secretaria`: Pautas da ordem do dia, gestão de sessões e atas.
- `financeiro`: Gestão de despesas, orçamentos e duodécimo.
- `compras`: Licitações, termos de referência e contratos (Lei 14.133/21).
- `almoxarife`: Movimentações de entrada e saída de estoque.
- `ouvidor`: Gestão do Fala.BR e pedidos da Lei de Acesso à Informação.
- `controle`: Acesso a trilhas de auditoria e apontamentos de riscos.

A proteção das páginas é imposta no topo de cada script:
```php
Auth::requireRole('admin'); // Bloqueia e redireciona se o papel não for atendido
```

### 5.3 Proteção Anti-CSRF
Todos os formulários POST do sistema exigem um token CSRF emitido via `generateCsrfToken()`:
```html
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
```
No processamento:
```php
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Acesso negado: Token CSRF inválido ou expirado.');
}
```

### 5.4 Prevenção contra SQL Injection e XSS
- **SQL Injection**: 100% das consultas utilizam `PDO::prepare()` com parâmetros nomeados ou posicionais. É terminantemente proibida a concatenação direta de variáveis em queries SQL.
- **XSS**: Toda e qualquer saída direcionada ao HTML utiliza o helper `escapeHtml($str)` ou `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`.

---

## 6. Motor de Inteligência Artificial e Pipeline RAG

O arquivo `includes/AIAssistant.php` gerencia o pipeline de **Retrieval-Augmented Generation (RAG)** e automações cognitivas da Câmara:

### 6.1 Arquitetura do Pipeline RAG

```
                      ┌──────────────────────────────┐
                      │    Pergunta do Parlamentar   │
                      │  "Qual regra sobre ruídos?"  │
                      └──────────────┬───────────────┘
                                     │
                                     ▼
                      ┌──────────────────────────────┐
                      │    Tokenização & Pesquisa    │
                      │      Semântica no MySQL      │
                      │  (Atos Oficiais e Leis Vigs) │
                      └──────────────┬───────────────┘
                                     │
                                     ▼
                      ┌──────────────────────────────┐
                      │     Ranking de Relevância    │
                      │   TF-IDF e Pontuação Lexical  │
                      └──────────────┬───────────────┘
                                     │
                                     ▼
                      ┌──────────────────────────────┐
                      │   Construção do Prompt RAG   │
                      │ [Contexto das Leis Encontr.] │
                      │  + [Instruções Normativas]   │
                      └──────────────┬───────────────┘
                                     │
                                     ▼
                      ┌──────────────────────────────┐
                      │    Geração da Resposta com   │
                      │ Citações de Artigos e Leis   │
                      └──────────────────────────────┘
```

### 6.2 Redator de Projetos de Lei (Conformidade com a LC 95/1998)
O assistente redige minutas seguindo a técnica legislativa federal:
- **Epígrafe e Ementa**: Síntese fiel do objeto do projeto.
- **Cláusula de Promulgação**: *"A Câmara Municipal aprova e eu sanciono..."*.
- **Estrutura de Artigos**: `Art. 1º`, `Art. 2º`, incisos (`I, II`), alíneas (`a, b`) e parágrafos (`§ 1º, Parágrafo único`).
- **Cláusula de Vigência**: Prazo de *vacatio legis* ou vigência na data da publicação.
- **Justificativa**: Análise de competência municipal (Art. 30 da CF/88) e fundamentação socioeconômica.

---

## 7. Especificação das APIs REST e Endpoints AJAX

Todos os endpoints residem no diretório `/api/` e operam com cabeçalho `Content-Type: application/json`:

### 7.1 `POST /api/ai_assistant.php`
- **Ações Disponíveis**:
  - `rag_search`: Busca semântica contextual nas leis do município.
  - `draft_bill`: Redação de minuta completa de Projeto de Lei.
  - `summarize_order`: Sumarização executiva da pauta da sessão.
- **Payload de Exemplo**:
  ```json
  {
    "action": "draft_bill",
    "theme": "Implantação de hortas comunitárias em terrenos públicos",
    "type": "Projeto de Lei Ordinária",
    "csrf_token": "a9f3b8c..."
  }
  ```
- **Resposta**:
  ```json
  {
    "success": true,
    "title": "Projeto de Lei nº ...",
    "content": "Art. 1º Fica instituído no Município...",
    "justification": "A presente proposição encontra respaldo..."
  }
  ```

### 7.2 `POST /api/vote_plenary.php`
- **Finalidade**: Registro do voto nominal de um vereador em plenário.
- **Parâmetros**: `sessao_id`, `proposicao_id`, `vereador_id`, `voto` (`SIM`, `NAO`, `ABSTENCAO`).
- **Garantia**: Restrição única `UNIQUE KEY (sessao_id, proposicao_id, vereador_id)` garantindo que o parlamentar vote apenas uma vez por matéria.

### 7.3 `POST /api/stock_action.php`
- **Finalidade**: Movimentação atômica de estoque (Entrada ou Saída).
- **Parâmetros**: `action` (`in` ou `out`), `item_id`, `quantity`, `destination`.
- **Garantia**: Em saídas, utiliza `SELECT estoque_atual FROM almox_itens WHERE id = ? FOR UPDATE` dentro de transação PDO; se a quantidade for superior ao saldo, lança exceção com rollback, impedindo saldos negativos.

### 7.4 `POST /api/switch_tenant.php`
- **Finalidade**: Alternância de Câmara Municipal pelo administrador.
- **Parâmetros**: `tenant_id`, `csrf_token`.
- **Resposta**: `{"success": true, "tenant_name": "Câmara Municipal de Testes"}`.

---

## 8. Engenharia do Sistema de Backup e Recuperação (DR)

A classe `includes/Backup.php` foi projetada para operar em qualquer ambiente (Windows/XAMPP, Linux/Docker, Cloud):

### 8.1 Estratégia Híbrida de Exportação
1. **Tentativa Primária via Binário `mysqldump`**:
   - Detecta o executável no caminho do XAMPP (`C:\xampp\mysql\bin\mysqldump.exe`) ou no `$PATH`.
   - Executa com parâmetros `--routines --triggers --single-transaction --default-character-set=utf8mb4`.
2. **Fallback Universal Nativo (Pure PHP PDO)**:
   - Se o binário não for detectado ou falhar, o sistema ativa imediatamente o dumper nativo construído em PHP.
   - Itera sobre todas as tabelas (`SHOW TABLES`), gera as instruções `DROP TABLE IF EXISTS`, extrai o DDL com `SHOW CREATE TABLE` e pagina as inserções (`INSERT INTO ... VALUES (...)`) em lotes de 100 registros com valores tratados via `$pdo->quote()`.

### 8.2 Integridade e Assinatura SHA-256
Para cada backup gerado, o sistema:
1. Calcula o Hash SHA-256 do arquivo resultante: `hash_file('sha256', $filepath)`.
2. Gera um arquivo de metadados correspondente `<arquivo>.sql.json` contendo timestamp, tamanho em bytes, SHA-256, método utilizado e notas de auditoria.
3. Registra a ação na tabela `audit_logs`.

### 8.3 Execução CLI para Agendamento (Cron / Windows Task Scheduler)
O script `backup.php` na raiz pode ser disparado periodicamente:
```bash
# Execução agendada noturna
php backup.php --create "Backup Noturno Diario - Tarefa Agendada"
```

---

## 9. Guia de Extensibilidade: Criando um Novo Módulo

Para adicionar uma nova funcionalidade ou módulo ao ecossistema da Câmara Digital, siga os passos abaixo:

### Passo 1: Criar a Tabela no Banco de Dados
Adicione a tabela com a coluna `tenant_id` no arquivo `sql/schema.sql` e execute no banco:
```sql
CREATE TABLE IF NOT EXISTS `meu_modulo_dados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `descricao` TEXT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Passo 2: Criar o Script de Interface
Crie o arquivo `modulos/meu_modulo.php`:
```php
<?php
require_once __DIR__ . '/../config/app.php';
Auth::requireAuth(); // Exige login

$pageTitle = 'Meu Novo Módulo';
$tenantId = Tenant::id();
$pdo = Database::getConnection();

// Lógica de consulta
$stmt = $pdo->prepare("SELECT * FROM meu_modulo_dados WHERE tenant_id = ? ORDER BY id DESC");
$stmt->execute([$tenantId]);
$itens = $stmt->fetchAll();

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold text-slate-900"><?= $pageTitle ?></h1>
        <!-- Renderização da tabela e formulários com Tailwind CSS -->
    </div>
</main>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>
```

### Passo 3: Registrar a Trilha de Auditoria
Sempre que uma operação de escrita (Create/Update/Delete) for executada, invoque o log de auditoria:
```php
Audit::log('CRIAR_ITEM', 'meu_modulo', $itemId, "Item '{$titulo}' cadastrado com sucesso.");
```

### Passo 4: Adicionar o Item de Navegação na Sidebar
Edite `includes/Sidebar.php` e inclua o link para o módulo dentro do bloco de navegação correspondente.

---

## 10. Instalação, Ambientes e Comandos de Manutenção

### 10.1 Requisitos Mínimos
- **Servidor Web**: Apache 2.4+ ou Nginx com PHP-FPM.
- **PHP**: Versão 8.2 ou superior (com extensões `pdo_mysql`, `mbstring`, `json`, `session`, `openssl`).
- **Banco de Dados**: MySQL 8.0+ ou MariaDB 10.4+.

### 10.2 Inicialização e Recarga do Banco de Dados
Para reinstalar ou popular o banco com os dados de demonstração:
```powershell
# Execução do instalador nativo
C:\xampp\php\php.exe c:\xampp\htdocs\camara_digital\install.php
```

### 10.3 Teste da Conectividade do Banco
```powershell
C:\xampp\php\php.exe -r "require 'config/database.php'; echo 'Conectado com sucesso ao MySQL: ' . Database::getConnection()->getAttribute(PDO::ATTR_SERVER_INFO) . PHP_EOL;"
```

---

## 11. Conformidade Regulatória v1.2, PNTP 2026 e Linguagem Simples

### 11.1 Política Nacional de Linguagem Simples (Lei Federal nº 15.263/2025)
- **Princípio da Não-Substituição**: A legislação veda a substituição do texto normativo oficial por resumos ou paráfrases de Inteligência Artificial. O texto legal original permanece juridicamente autêntico, enquanto o resumo explicativo é renderizado em aba ou painel auxiliar destacado ("Linguagem Simples - Munícipe").
- **Campos do Schema**:
  - `legislative_matters.plain_language_summary` (TEXT NULL)
  - `legal_norms.plain_language_summary` (TEXT NULL)
- **Geração via IA**: Ação `plain_language` no endpoint `/api/ai_assistant.php` com heurística legislativa semântica, traduzindo juridiquês para linguagem direta e acessível ao munícipe.

### 11.2 Níveis de Assinatura Eletrônica (Lei Federal nº 14.063/2020)
- **Simples**: Identificação do signatário por meio de login e senha no sistema municipal.
- **Avançada**: Uso de certificado corporativo municipal, hash SHA-256 de integridade e registro temporal rastreável.
- **Qualificada (ICP-Brasil)**: Certificado digital emitido pela cadeia ICP-Brasil com não repúdio irrestrito.
- **Parametrização**: Campo `signature_level` em `legislative_matters`.

### 11.3 Workflow de Publicação Oficial e Metadados Permanentes
- **Distinção Operacional**: O envio de matéria para diário oficial não equivale à publicação formal. A plataforma armazena o ciclo de vida completo:
  - `official_publication_state` (`nao_publicado`, `em_processamento`, `publicado`)
  - `official_publication_vehicle` (ex: "Diário Oficial dos Municípios / ASSOMASUL")
  - `official_publication_edition` (número da edição oficial)
  - `official_publication_date` (data da circulação oficial)
  - `official_publication_url` (link permanente de verificação)

### 11.4 Preparação Radar da Transparência Pública (PNTP 2026 - Atricon / Tribunais de Contas)
- Tabela `pntp_checklist` estruturada com 22 critérios de transparência ativa essenciais para avaliação de Tribunais de Contas.
- Monitoramento de conformidade com URLs públicas de evidência direta.
- **Regra de Compliance Ético**: A plataforma expressamente não emite selos falsos ou pontuações simuladas com menção a 100% de conformidade, identificando o painel estritamente como *Checklist de Preparação PNTP 2026*, cabendo a nota final aos órgãos de controle externo.

### 11.5 Acessibilidade Governamental (eMAG e WCAG 2.1 AA)
- Componente `includes/PublicHeader.php` implementa a Barra de Acessibilidade Governamental padrão:
  - **Atalhos de Teclado**: Alt+1 (Ir para Conteúdo), Alt+2 (Ir para Menu), Alt+4 (Ir para Contraste).
  - **Alto Contraste**: Inversão estilizada de alto contraste via classe CSS `.high-contrast` (fundo preto `#000000`, textos e bordas em `#ffff00` e `#00ffff`).
  - **Controle Tipográfico**: Redimensionamento dinâmico de fonte (A+, A-, Normal) persistido em `localStorage`.

---

*Documentação Técnica desenvolvida sob os padrões de excelência de Engenharia de Software da plataforma Câmara Digital.*
