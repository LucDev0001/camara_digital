# Câmara Digital — Plataforma SaaS Multi-Câmara de Gestão Integrada

> Sistema completo e funcional para Câmaras Municipais brasileiras, desenvolvido com **PHP 8.2+**, **MySQL**, **Tailwind CSS**, **JavaScript (ES6+)** e **HTML5 Semântico**, em conformidade com o documento de planejamento mestre `PLANEJAMENTO_CAMARA_DIGITAL.md`.

---

## 🏛️ Visão Geral da Arquitetura

O **Câmara Digital** integra em uma única plataforma modular:
1. **Portal Público Oficial do Cidadão** (Transparência ativa, consulta de leis, acompanhamento de projetos, pautas de plenário, ouvidoria/SIC e validador de fé pública documental).
2. **Painel de Governança Integrado Multi-Tenant** (SaaS com suporte a múltiplas Câmaras no mesmo banco relacional, isoladas por `tenant_id`).
3. **Controle de Acesso RBAC** (Administrador, Presidência/Mesa Diretora, Vereadores, Secretaria Legislativa, RH, Almoxarifado, Compras, Financeiro, Controle Interno, Ouvidoria).
4. **Inteligência Artificial Legislativa & RAG** (Recuperação Aumentada por Geração com citação de fontes legais e redator assistido de proposições).
5. **Trilha Global de Auditoria** (Rastreabilidade integral de acessos, votos, despachos e movimentações com IP e carimbo de data/hora).

---

## 📦 Estrutura de Diretórios

```
c:\xampp\htdocs\camara_digital\
├── config/
│   ├── database.php          # Conexão singleton PDO com MySQL (UTF-8 MB4)
│   └── app.php               # Configurações globais, sessões, helpers e proteção CSRF
├── includes/
│   ├── Tenant.php            # Contexto multi-tenant e alternância de Câmaras
│   ├── Auth.php              # Autenticação segura (bcrypt), RBAC e login rápido
│   ├── Audit.php             # Sistema de gravação da trilha de auditoria
│   ├── AIAssistant.php       # Motor RAG com busca semântica em leis e minutas
│   ├── Header.php            # Cabeçalho do painel administrativo
│   ├── Sidebar.php           # Navegação pelas 12 áreas institucionais
│   ├── Footer.php            # Rodapé administrativo com busca rápida (Ctrl + K)
│   ├── PublicHeader.php      # Cabeçalho do Portal Público do Cidadão
│   └── PublicFooter.php      # Rodapé institucional do cidadão
├── modulos/                  # As 12 Áreas Funcionais do Planejamento
│   ├── administracao.php     # Área 1: Dados institucionais, setores, mesa e usuários
│   ├── legislativo.php       # Área 2: Proposições, tramitação, plenário virtual e votação
│   ├── rh.php                # Área 3: Servidores, cargos, férias e frequência/ponto
│   ├── almoxarifado.php      # Área 4: Catálogo de materiais, estoque auditado e requisições
│   ├── compras.php           # Área 5: Licitações Lei 14.133/21, contratos e fiscais
│   ├── patrimonio.php        # Área 6: Bens tombados, inventário e frota oficial
│   ├── financeiro.php        # Área 7: Dotações orçamentárias, despesas e diárias
│   ├── transparencia.php     # Área 8: Gestão de dados abertos e exportações CSV
│   ├── ouvidoria.php         # Área 9: Atendimento interno da ouvidoria e SIC
│   ├── protocolo.php         # Área 10: Protocolo geral, despachos e hash SHA-256
│   ├── controle_interno.php  # Área 11: Matriz de riscos, achados e conformidade
│   └── ia_legislativa.php    # Área 12: Assistente de IA, RAG e redator de minutas
├── portal/                   # Portal Público do Munícipe (Front-Facing)
│   ├── index.php             # Página inicial com notícias, banner e acesso rápido
│   ├── leis.php              # Acervo pesquisável de leis e normas municipais
│   ├── proposicoes.php       # Acompanhamento público de matérias e tramitação
│   ├── sessoes.php           # Sessões plenárias, ordem do dia e histórico de votos
│   ├── transparencia.php     # Portal da Transparência (LAI 12.527 / LRF LC 101)
│   ├── ouvidoria.php         # Formulário de manifestação e consulta por protocolo
│   ├── validar.php           # Validador público de autenticidade (Hash SHA-256)
│   └── noticias.php          # Notícias institucionais e notas da assessoria
├── api/                      # Endpoints AJAX REST
│   ├── ai_assistant.php      # Consulta RAG, redator de minutas e síntese
│   ├── vote_plenary.php      # Registro de votos nominais em plenário
│   ├── stock_action.php      # Entradas, saídas e aprovação de requisições de estoque
│   ├── protocol_action.php   # Autuação de protocolos e despachos eletrônicos
│   ├── citizen_action.php    # Submissão de manifestações e respostas da ouvidoria
│   ├── switch_tenant.php     # Alternância instantânea de Câmara / Tenant
│   └── quick_search.php      # Busca global rápida em leis e proposições
├── database/
│   ├── schema.sql            # Esquema relacional DDL (34 tabelas completas)
│   └── seed.sql              # Dados realistas completos de demonstração
├── assets/
│   ├── css/style.css         # Estilos customizados, fontes Google e micro-interações
│   └── js/app.js             # Toasts, modais dinâmicos, filtros de tabela e atalhos
├── dashboard.php             # Painel executivo integrado com métricas e gráficos Chart.js
├── login.php                 # Tela de login seguro com seleção de perfis de demonstração
├── logout.php                # Encerramento seguro de sessão
├── install.php               # Instalador e migrador automatizado (Web e CLI)
└── index.php                 # Ponto de entrada raiz (redireciona para o Portal)
```

---

## 🚀 Como Executar Localmente

### 1. Pré-requisitos
- **XAMPP** com **Apache** e **MySQL** em execução.
- PHP 8.2 ou superior habilitado.

### 2. Banco de Dados e Instalação
O banco de dados e as sementes já foram inicializados. Caso deseje reinstalar ou resetar o banco a qualquer momento:

- **Via Navegador:** Acesse `http://localhost/camara_digital/install.php`
- **Via Terminal / PowerShell:**
```powershell
cd c:\xampp\htdocs\camara_digital
php install.php
```

---

## 🔑 Acessos e Perfis de Demonstração

Para facilitar testes e apresentações, a tela de login (`/login.php`) oferece **botões de login rápido com 1 clique** para os principais perfis:

| Perfil | E-mail | Senha Padrão | Escopo de Acesso |
|---|---|---|---|
| **Administrador Geral** | `admin@camara.ms.gov.br` | `123456` | Acesso total irrestrito a todos os 12 módulos e auditoria |
| **Presidente da Câmara** | `presidente@camara.ms.gov.br` | `123456` | Mesa Diretora, sessões plenárias e pautas |
| **Vereadora (Mariana)** | `mariana@camara.ms.gov.br` | `123456` | Apresentação de proposições e votação em plenário |
| **Secretaria Legislativa** | `secretaria@camara.ms.gov.br` | `123456` | Tramitação, redação final, comissões e atas |
| **Almoxarifado** | `almoxarife@camara.ms.gov.br` | `123456` | Controle de estoque, entradas, saídas e requisições |
| **Ouvidoria / SIC** | `ouvidoria@camara.ms.gov.br` | `123456` | Atendimento ao cidadão e respostas da LAI |

---

## 🌐 Principais Endereços (URLs)

- **Portal Público do Cidadão:** `http://localhost/camara_digital/portal/index.php`
- **Portal da Transparência:** `http://localhost/camara_digital/portal/transparencia.php`
- **Consulta de Leis:** `http://localhost/camara_digital/portal/leis.php`
- **Ouvidoria & SIC:** `http://localhost/camara_digital/portal/ouvidoria.php`
- **Validador de Autenticidade (SHA-256):** `http://localhost/camara_digital/portal/validar.php`
- **Login dos Servidores:** `http://localhost/camara_digital/login.php`
- **Painel Geral (Dashboard):** `http://localhost/camara_digital/dashboard.php`
- **IA Legislativa & RAG:** `http://localhost/camara_digital/modulos/ia_legislativa.php`
- **Plenário Virtual e Votações:** `http://localhost/camara_digital/modulos/legislativo.php`
- **Backup & Recuperação (DR):** `http://localhost/camara_digital/modulos/backup.php`

---

## 📚 Documentação e Guias do Sistema

1. **[Guia Completo do Usuário (Manual Operacional)](GUIA_DO_USUARIO.md)**: Manual detalhado para cidadãos, vereadores, secretários, compradores e administradores com instruções passo a passo para cada um dos 12 módulos e portal público.
2. **[Documentação Técnica para Desenvolvedores](DOCUMENTACAO_TECNICA.md)**: Arquitetura completa, modelo de dados das 34 tabelas, RBAC, pipeline RAG com IA, endpoints de API REST/AJAX e diretrizes de extensibilidade.
3. **[Sistema de Backup e Recuperação](modulos/backup.php)**: Ferramenta integrada de disaster recovery com exportação híbrida (`mysqldump` + PHP PDO nativo), verificação de integridade SHA-256 e suporte a execução automatizada via CLI (`php backup.php --create`).

